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
            $isAdditional = false;
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

            if ($transaction && $transaction->details->count() > 0) {
                $isAdditional = true;
            }

            if (!$transaction) {
                $transaction = Transaction::create([
                    'user_id' => auth()->id(),
                    'transaction_number' => 'TRX-' . date('YmdHis') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                    'order_source' => 'pos',
                    'order_status' => 'confirmed',
                    'payment_status' => 'pending',
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
                    'price' => $items[0]['price'] ?? 0,
                    'notes' => $allNotes ?: null
                ];
            });

            $existingDetails = $transaction->details->keyBy('product_id');
            $newTotal = 0;
            $printItems = [];

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
                        $existing->qty = $newQty;
                        $existing->notes = $item['notes'];
                        $existing->save();
                    }
                    $existingDetails->forget($productId);
                } else {
                    if ($product->stock < $newQty) throw new \Exception("Stok {$product->name} tidak mencukupi!");
                    $product->decrement('stock', $newQty);
                    StockLog::create(['product_id' => $productId, 'qty_out' => $newQty]);
                    
                    $existing = TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'qty' => $newQty,
                        'qty_printed' => 0,
                        'price' => $actualPrice, // Gunakan actual price
                        'notes' => $item['notes']
                    ]);
                }

                // Reset qty_printed if quantity was decreased
                if ($existing->qty_printed > $existing->qty) {
                    $existing->qty_printed = $existing->qty;
                    $existing->save();
                }

                // Calculate unsent items for kitchen ticket
                $qtyToPrint = $existing->qty - $existing->qty_printed;
                if ($qtyToPrint > 0) {
                    $printItems[] = [
                        'product_id' => $productId,
                        'qty' => $qtyToPrint,
                        'notes' => $existing->notes
                    ];
                    
                    $existing->qty_printed = $existing->qty;
                    $existing->save();
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
            return response()->json([
                'success' => true, 
                'transaction_id' => $transaction->id,
                'print_items' => $printItems,
                'is_additional' => $isAdditional
            ]);
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
                'order_status' => 'completed',
                'payment_status' => 'paid',
                'payment_method' => $request->payment_method,
                'discount' => $discount
            ]);

            $table = DiningTable::where('name', $transaction->table_number)->first();
            if ($table) {
                $table->status = 'available';
                $table->save();
                \App\Models\OrderSession::where('dining_table_id', $table->id)->where('status', 'active')->update(['status' => 'closed']);
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

    public function printKitchenTicket(Request $request, $id)
    {
        $transaction = Transaction::with(['details.product', 'user'])->findOrFail($id);
        $isAdditional = $request->has('additional') && $request->additional == 1;
        
        if ($request->has('items')) {
            try {
                $itemsRaw = $request->query('items');
                $itemsDecoded = stripslashes($itemsRaw);
                $printItems = json_decode($itemsDecoded, true);
                if (is_array($printItems)) {
                    $printItemsMap = collect($printItems)->keyBy('product_id');
                    $detailsToPrint = collect();
                    
                    foreach ($transaction->details as $detail) {
                        if ($printItemsMap->has($detail->product_id)) {
                            $printItem = $printItemsMap->get($detail->product_id);
                            $detail->qty_to_print = $printItem['qty'];
                            if (isset($printItem['notes'])) {
                                $detail->notes = $printItem['notes'];
                            }
                            $detailsToPrint->push($detail);
                        }
                    }
                    $transaction->setRelation('details', $detailsToPrint);
                }
            } catch (\Exception $e) {
                \Log::error("Kitchen ticket print error: " . $e->getMessage());
            }
        }
        
        if (!isset($detailsToPrint) || $detailsToPrint->isEmpty()) {
            foreach ($transaction->details as $detail) {
                $detail->qty_to_print = $detail->qty;
            }
        }
        
        return view('pos.kitchen_ticket', compact('transaction', 'isAdditional'));
    }

    public function history(Request $request)
    {
        $today = \Carbon\Carbon::today();
        $todayPaidTrx = Transaction::whereDate('created_at', $today)->where('status', 'paid')->get();
        $todaySummary = [
            'total' => $todayPaidTrx->sum('total'),
            'count' => $todayPaidTrx->count(),
            'pos' => $todayPaidTrx->where('order_source', 'pos')->sum('total'),
            'pos_count' => $todayPaidTrx->where('order_source', 'pos')->count(),
            'qr' => $todayPaidTrx->where('order_source', 'qr')->sum('total'),
            'qr_count' => $todayPaidTrx->where('order_source', 'qr')->count(),
            'cash' => $todayPaidTrx->where('payment_method', 'Cash')->sum('total'),
            'qris' => $todayPaidTrx->whereIn('payment_method', ['QRIS', 'Mock Gateway'])->sum('total'),
        ];

        $query = Transaction::with(['user', 'voidLog', 'details.product']);

        if ($request->filled('filter_type')) {
            $type = $request->filter_type;
            if ($type === 'date' && $request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            } elseif ($type === 'month' && $request->filled('month')) {
                $parts = explode('-', $request->month);
                if (count($parts) === 2) {
                    $query->whereYear('created_at', $parts[0])
                          ->whereMonth('created_at', $parts[1]);
                } else {
                    $query->whereMonth('created_at', $request->month);
                }
            } elseif ($type === 'year' && $request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        return view('pos.history', compact('transactions', 'todaySummary'));
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
        $transactions = Transaction::with(['user', 'details.product'])
            ->where(function($q) {
                $q->where('status', 'unpaid')
                  ->orWhere(function($sub) {
                      $sub->where('order_source', 'qr')
                          ->whereIn('order_status', ['paid', 'confirmed', 'preparing', 'ready']);
                  });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($transactions);
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

        // Tutup sesi order aktif meja
        \App\Models\OrderSession::where('dining_table_id', $table->id)->where('status', 'active')->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    /**
     * Polling Pesanan Masuk dari Channel QR
     */
    public function getIncomingQrOrders()
    {
        $orders = Transaction::with(['details.product'])
            ->where('order_source', 'qr')
            ->whereIn('order_status', ['paid', 'confirmed', 'preparing', 'ready'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $orders->count(),
            'orders' => $orders
        ]);
    }

    /**
     * Kasir Konfirmasi Pesanan QR (Mulai Masak)
     */
    public function confirmQrOrder($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['order_status' => 'preparing']);

        return response()->json(['success' => true, 'order_status' => 'preparing']);
    }

    /**
     * Update Status Order (preparing -> ready -> completed)
     */
    public function updateOrderStatus(Request $request, $id)
    {
        $newStatus = $request->order_status ?: $request->status;

        if (!$newStatus || !in_array($newStatus, ['confirmed', 'preparing', 'ready', 'completed', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Status pesanan tidak valid.'
            ], 422);
        }

        $transaction = Transaction::findOrFail($id);
        $transaction->update(['order_status' => $newStatus]);

        if ($newStatus === 'completed') {
            $table = DiningTable::where('name', $transaction->table_number)->first();
            if ($table) {
                $otherActive = Transaction::where('table_number', $table->name)
                    ->where('id', '!=', $transaction->id)
                    ->whereIn('order_status', ['confirmed', 'preparing', 'ready'])
                    ->exists();

                if (!$otherActive) {
                    $table->update(['status' => 'available']);
                    \App\Models\OrderSession::where('dining_table_id', $table->id)->where('status', 'active')->update(['status' => 'closed']);
                }
            }
        }

        return response()->json(['success' => true, 'order_status' => $newStatus]);
    }

    /**
     * Lacak Pendapatan Masuk Hari Ini beserta rincian transaksi (POS Kasir & QR Meja)
     */
    public function getTodayIncome()
    {
        $today = \Carbon\Carbon::today();

        $transactions = Transaction::with(['details.product', 'user'])
            ->whereDate('created_at', $today)
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = (float) $transactions->sum('total');
        $posRevenue = (float) $transactions->where('order_source', 'pos')->sum('total');
        $qrRevenue = (float) $transactions->where('order_source', 'qr')->sum('total');

        $cashTotal = (float) $transactions->where('payment_method', 'Cash')->sum('total');
        $qrisTotal = (float) $transactions->whereIn('payment_method', ['QRIS', 'Mock Gateway'])->sum('total');
        $debitTotal = (float) $transactions->where('payment_method', 'Debit')->sum('total');

        $transactionList = $transactions->map(function ($trx) {
            return [
                'id' => $trx->id,
                'transaction_number' => $trx->transaction_number ?: 'TRX-' . str_pad($trx->id, 5, '0', STR_PAD_LEFT),
                'time' => $trx->created_at->format('H:i'),
                'datetime' => $trx->created_at->format('d/m/Y H:i'),
                'table_number' => $trx->table_number ?: 'Bawa Pulang',
                'customer_name' => $trx->customer_name ?: 'Pelanggan',
                'order_source' => $trx->order_source ?: 'pos',
                'order_source_label' => ($trx->order_source === 'qr') ? 'QR Meja' : 'POS Kasir',
                'payment_method' => $trx->payment_method ?: 'Cash',
                'payment_status' => $trx->payment_status ?: 'paid',
                'order_status' => $trx->order_status ?: 'completed',
                'total' => (float) $trx->total,
                'discount' => (float) ($trx->discount ?? 0),
                'cashier_name' => $trx->user ? $trx->user->name : ($trx->order_source === 'qr' ? 'Self-Order' : 'Kasir'),
                'items_count' => $trx->details->sum('qty'),
                'items_summary' => $trx->details->map(function ($d) {
                    return $d->qty . 'x ' . ($d->product ? $d->product->name : 'Item');
                })->implode(', '),
                'details' => $trx->details->map(function ($d) {
                    return [
                        'product_name' => $d->product ? $d->product->name : 'Item Terhapus',
                        'qty' => $d->qty,
                        'price' => (float) $d->price,
                        'subtotal' => (float) ($d->qty * $d->price),
                        'notes' => $d->notes ?: null
                    ];
                })
            ];
        });

        return response()->json([
            'success' => true,
            'summary' => [
                'date_formatted' => $today->translatedFormat('d F Y'),
                'total_revenue' => $totalRevenue,
                'total_revenue_formatted' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'),
                'count' => $transactions->count(),
                'pos_revenue' => $posRevenue,
                'pos_count' => $transactions->where('order_source', 'pos')->count(),
                'qr_revenue' => $qrRevenue,
                'qr_count' => $transactions->where('order_source', 'qr')->count(),
                'cash_total' => $cashTotal,
                'cash_count' => $transactions->where('payment_method', 'Cash')->count(),
                'qris_total' => $qrisTotal,
                'qris_count' => $transactions->whereIn('payment_method', ['QRIS', 'Mock Gateway'])->count(),
                'debit_total' => $debitTotal,
                'debit_count' => $transactions->where('payment_method', 'Debit')->count(),
            ],
            'transactions' => $transactionList
        ]);
    }
}
