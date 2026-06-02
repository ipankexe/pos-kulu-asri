<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockLog;
use App\Models\DiningTable;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Product::with('category');
        
        if($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        if($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->get();

        return view('pos.index', compact('categories', 'products'));
    }

    public function saveOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'table_number' => 'required|string',
            'cart' => 'required|array',
            'transaction_id' => 'nullable|integer'
        ]);

        DB::beginTransaction();
        try {
            $transaction = null;
            if ($request->transaction_id) {
                $transaction = Transaction::with('details')->where('id', $request->transaction_id)
                                          ->where('status', 'unpaid')
                                          ->first();
            }

            if (!$transaction) {
                if ($request->table_number !== 'Takeaway') {
                    $existing = Transaction::with('details')->where('table_number', $request->table_number)
                                           ->where('status', 'unpaid')
                                           ->first();
                    if ($existing) {
                        $transaction = $existing;
                    }
                }
            }

            if (!$transaction) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'transaction_number' => 'TRX-' . date('YmdHis') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                    'total' => 0,
                    'payment' => 0,
                    'change' => 0,
                    'status' => 'unpaid',
                    'payment_method' => 'Cash',
                    'discount' => 0
                ]);
                // Eager load empty details relation
                $transaction->load('details');

                $table = DiningTable::where('name', $request->table_number)->first();
                if ($table) {
                    $table->status = 'occupied';
                    $table->save();
                }
            } else {
                // Update customer name if it changed
                $transaction->customer_name = $request->customer_name;
            }

            // Group cart by product ID
            $cartItems = collect($request->cart)->groupBy('id')->map(function($items) {
                $allNotes = $items->pluck('notes')->filter(function($value) { return !is_null($value) && $value !== ''; })->implode(', ');
                return [
                    'id' => $items[0]['id'],
                    'qty' => $items->sum('qty'),
                    'price' => $items[0]['price'],
                    'notes' => $allNotes ?: null
                ];
            });

            $existingDetails = $transaction->details->keyBy('product_id');
            $newTotal = 0;

            foreach ($cartItems as $productId => $item) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                if (!$product) continue;

                $newQty = $item['qty'];
                // Mencegah manipulasi harga dari frontend, gunakan harga aktual dari DB
                $actualPrice = $product->price;
                $newTotal += $actualPrice * $newQty;

                if ($existingDetails->has($productId)) {
                    $existing = $existingDetails->get($productId);
                    $diff = $newQty - $existing->qty;

                    if ($diff > 0) {
                        if ($product->stock < $diff) throw new \Exception("Stok {$product->name} tidak mencukupi!");
                        $product->decrement('stock', $diff);
                        StockLog::create(['product_id' => $productId, 'qty_out' => $diff]);
                    } elseif ($diff < 0) {
                        $absDiff = abs($diff);
                        $product->increment('stock', $absDiff);
                        StockLog::create(['product_id' => $productId, 'qty_out' => -$absDiff]);
                    }

                    if ($diff != 0 || $existing->notes !== $item['notes']) {
                        $existing->update(['qty' => $newQty, 'notes' => $item['notes']]);
                    }
                    $existingDetails->forget($productId);
                } else {
                    if ($product->stock < $newQty) throw new \Exception("Stok {$product->name} tidak mencukupi!");
                    $product->decrement('stock', $newQty);
                    StockLog::create(['product_id' => $productId, 'qty_out' => $newQty]);
                    
                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'qty' => $newQty,
                        'price' => $actualPrice, // Gunakan actual price
                        'notes' => $item['notes']
                    ]);
                }
            }

            // Removed items
            foreach ($existingDetails as $productId => $existing) {
                $product = Product::where('id', $productId)->lockForUpdate()->first();
                if ($product) {
                    $product->increment('stock', $existing->qty);
                    StockLog::create(['product_id' => $productId, 'qty_out' => -$existing->qty]);
                }
                $existing->delete();
            }

            $transaction->total = $newTotal;
            $transaction->save();

            DB::commit();
            return response()->json(['success' => true, 'transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            if (!str_starts_with($msg, 'Stok ')) {
                \Log::error($e);
                $msg = 'Terjadi kesalahan sistem internal. Silakan hubungi admin.';
            }
            return response()->json(['success' => false, 'message' => $msg]);
        }
    }

    public function payOrder(Request $request, $id)
    {
        $request->validate([
            'payment' => 'required|numeric',
            'payment_method' => 'required|in:Cash,QRIS,Debit',
            'discount' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)->where('status', 'unpaid')->firstOrFail();

            $discount = $request->discount ?? 0;
            $subtotal = $transaction->total;
            
            if ($discount > $subtotal) {
                throw new \Exception("Diskon tidak boleh melebihi subtotal tagihan!");
            }
            
            $finalTotal = max(0, $subtotal - $discount);
            
            $payment = $request->payment;
            if ($request->payment_method !== 'Cash') {
                $payment = $finalTotal;
            }
            
            $change = $payment - $finalTotal;

            if ($change < 0) {
                return response()->json(['success' => false, 'message' => 'Uang pembayaran kurang!']);
            }

            $transaction->update([
                'total' => $finalTotal,
                'payment' => $payment,
                'change' => $change,
                'status' => 'paid',
                'payment_method' => $request->payment_method,
                'discount' => $discount
            ]);

            $table = DiningTable::where('name', $transaction->table_number)->first();
            if ($table) {
                $table->status = 'available';
                $table->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'transaction_id' => $transaction->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $e->getMessage();
            if (!str_starts_with($msg, 'Uang ') && !str_starts_with($msg, 'Diskon ')) {
                \Log::error($e);
                $msg = 'Terjadi kesalahan sistem internal saat pembayaran. Silakan hubungi admin.';
            }
            return response()->json(['success' => false, 'message' => $msg]);
        }
    }

    public function getActiveOrder($table_name)
    {
        $transaction = Transaction::with('details.product')
            ->where('table_number', $table_name)
            ->where('status', 'unpaid')
            ->first();

        if ($transaction) {
            return response()->json([
                'success' => true,
                'transaction' => $transaction
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function printReceipt($id)
    {
        $transaction = Transaction::with(['details.product', 'user'])->findOrFail($id);
        return view('pos.receipt', compact('transaction'));
    }

    public function printKitchenTicket($id)
    {
        $transaction = Transaction::with(['details.product', 'user'])->findOrFail($id);
        return view('pos.kitchen_ticket', compact('transaction'));
    }

    public function history()
    {
        $transactions = Transaction::with('user', 'voidLog')->orderBy('created_at', 'desc')->paginate(20);
        return view('pos.history', compact('transactions'));
    }

    public function voidTransaction(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        
        DB::beginTransaction();
        try {
            $transaction = Transaction::with('details')->findOrFail($id);
            if($transaction->status === 'void') {
                throw new \Exception("Transaksi sudah di-void sebelumnya!");
            }
            
            $transaction->update(['status' => 'void']);
            
            \App\Models\VoidLog::create([
                'transaction_id' => $transaction->id,
                'reason' => $request->reason,
                'void_by' => auth()->user()->name
            ]);
            
            if ($transaction->table_number) {
                $table = \App\Models\DiningTable::where('name', $transaction->table_number)->first();
                if ($table) {
                    $table->status = 'available';
                    $table->save();
                }
            }
            
            foreach($transaction->details as $detail) {
                $product = \App\Models\Product::where('id', $detail->product_id)->lockForUpdate()->first();
                if($product) {
                    $product->increment('stock', $detail->qty);
                    \App\Models\StockLog::create([
                        'product_id' => $product->id,
                        'qty_out' => -$detail->qty
                    ]);
                }
            }
            
            DB::commit();
            return back()->with('success', 'Transaksi berhasil di-void dan stok dikembalikan.');
        } catch(\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function eodSummary()
    {
        $today = \Carbon\Carbon::today();
        $transactions = Transaction::whereDate('created_at', $today)->where('user_id', auth()->id())->get();
        
        return response()->json([
            'total_sales' => $transactions->where('status', 'paid')->sum('total'),
            'total_discount' => $transactions->where('status', 'paid')->sum('discount'),
            'cash' => $transactions->where('status', 'paid')->where('payment_method', 'Cash')->sum('total'),
            'qris' => $transactions->where('status', 'paid')->where('payment_method', 'QRIS')->sum('total'),
            'debit' => $transactions->where('status', 'paid')->where('payment_method', 'Debit')->sum('total'),
            'void_count' => $transactions->where('status', 'void')->count(),
            'transaction_count' => $transactions->where('status', 'paid')->count()
        ]);
    }

    public function printEod()
    {
        $today = \Carbon\Carbon::today();
        $transactions = Transaction::whereDate('created_at', $today)->where('user_id', auth()->id())->get();
        
        $summary = [
            'date' => now()->format('d/m/Y H:i'),
            'kasir' => auth()->user()->name,
            'total_sales' => $transactions->where('status', 'paid')->sum('total'),
            'total_discount' => $transactions->where('status', 'paid')->sum('discount'),
            'cash' => $transactions->where('status', 'paid')->where('payment_method', 'Cash')->sum('total'),
            'qris' => $transactions->where('status', 'paid')->where('payment_method', 'QRIS')->sum('total'),
            'debit' => $transactions->where('status', 'paid')->where('payment_method', 'Debit')->sum('total'),
            'void_count' => $transactions->where('status', 'void')->count(),
            'transaction_count' => $transactions->where('status', 'paid')->count()
        ];
        
        return view('pos.eod_receipt', compact('summary'));
    }

    public function getActiveTransactions()
    {
        return response()->json(Transaction::with('user')->where('status', 'unpaid')->get());
    }

    public function getTransaction($id)
    {
        $transaction = Transaction::with('details.product')->findOrFail($id);
        return response()->json(['success' => true, 'transaction' => $transaction]);
    }

    public function getTables()
    {
        return response()->json(DiningTable::orderBy('id')->get());
    }

    public function freeTable($id)
    {
        $table = DiningTable::findOrFail($id);
        
        $transaction = Transaction::with('details')
            ->where('table_number', $table->name)
            ->where('status', 'unpaid')
            ->first();
            
        if ($transaction) {
            DB::beginTransaction();
            try {
                $transaction->update(['status' => 'void']);
                
                \App\Models\VoidLog::create([
                    'transaction_id' => $transaction->id,
                    'reason' => 'Meja Dikosongkan Manual',
                    'void_by' => auth()->user() ? auth()->user()->name : 'System'
                ]);
                
                foreach($transaction->details as $detail) {
                    $product = \App\Models\Product::where('id', $detail->product_id)->lockForUpdate()->first();
                    if($product) {
                        $product->increment('stock', $detail->qty);
                        \App\Models\StockLog::create([
                            'product_id' => $product->id,
                            'qty_out' => -$detail->qty
                        ]);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Gagal mengosongkan meja karena error sistem.']);
            }
        }
        
        $table->status = 'available';
        $table->save();
        return response()->json(['success' => true]);
    }
}
