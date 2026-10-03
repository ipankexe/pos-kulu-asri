<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiningTable;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\OrderSession;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerOrderController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Halaman Pemilihan Meja (Untuk Testing / Tanpa Scan)
     */
    public function tablesList()
    {
        $tables = DiningTable::where('is_active', true)->orderBy('id')->get();
        return view('customer.tables_list', compact('tables'));
    }

    /**
     * Halaman Utama Menu Pelanggan (Scan QR Meja)
     */
    public function showMenu($token, Request $request)
    {
        // Keamanan: hanya token QR yang cocok persis yang diterima.
        // Tidak ada pencarian via ID / nama meja / potongan token, agar meja tidak bisa
        // dibuka dari rumah dengan menebak URL dan agar fitur "Regenerate QR" benar-benar efektif.
        $table = DiningTable::where('qr_token', $token)->first();

        if (!$table) {
            return response()->view('customer.error', [
                'title' => 'QR Code Tidak Dikenali',
                'message' => 'QR Code meja yang Anda scan tidak terdaftar di sistem Rumah Makan Kulu Asri. Silakan hubungi kasir atau pelayan.'
            ], 404);
        }

        if (!$table->is_active) {
            return response()->view('customer.error', [
                'title' => 'Meja Sedang Tidak Aktif',
                'message' => "Meja {$table->name} saat ini sedang dinonaktifkan untuk pemesanan. Silakan hubungi kasir atau pelayan."
            ], 403);
        }

        // Kelola sesi meja
        $session = OrderSession::where('dining_table_id', $table->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$session) {
            $session = OrderSession::create([
                'dining_table_id' => $table->id,
                'session_token' => Str::random(40),
                'status' => 'active'
            ]);
        }

        // Cek apakah ada pesanan yang sedang berjalan di meja ini
        $activeOrder = Transaction::with(['details.product'])
            ->where('table_number', $table->name)
            ->whereIn('order_status', ['confirmed', 'preparing', 'ready'])
            ->latest()
            ->first();

        $categories = Category::with(['products' => function ($q) {
            $q->orderBy('name', 'asc');
        }])->orderBy('name', 'asc')->get();

        $allProducts = Product::with('category')->orderBy('name', 'asc')->get();

        return view('customer.menu', compact('table', 'session', 'categories', 'allProducts', 'activeOrder'));
    }

    /**
     * Endpoint API Data Menu & Kategori
     */
    public function getMenuData($token)
    {
        $table = DiningTable::where('qr_token', $token)->where('is_active', true)->firstOrFail();
        $categories = Category::all();
        $products = Product::all();

        return response()->json([
            'success' => true,
            'table' => [
                'id' => $table->id,
                'name' => $table->name,
            ],
            'categories' => $categories,
            'products' => $products
        ]);
    }

    /**
     * Checkout Keranjang Pelanggan
     */
    public function checkout($token, Request $request)
    {
        $request->validate([
            'customer_name' => 'nullable|string|max:100',
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|integer|exists:products,id',
            'cart.*.qty' => 'required|integer|min:1',
            'cart.*.notes' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string'
        ]);

        $table = DiningTable::where('qr_token', $token)->first();

        if (!$table || !$table->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Meja tidak ditemukan atau sedang tidak aktif.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Group cart by product ID
            $cartItems = collect($request->cart)->groupBy('id')->map(function ($items) {
                $allNotes = $items->pluck('notes')->filter()->implode(', ');
                return [
                    'id' => $items[0]['id'],
                    'qty' => $items->sum('qty'),
                    'notes' => $allNotes ?: null
                ];
            });

            $calculatedTotal = 0;
            $validatedItems = [];

            // Validasi stok dan hitung ulang harga dari server
            foreach ($cartItems as $productId => $item) {
                $product = Product::where('id', $productId)->first();

                if (!$product) {
                    throw new \Exception("Produk tidak ditemukan.");
                }

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi (sisa: {$product->stock}).");
                }

                $subtotal = $product->price * $item['qty'];
                $calculatedTotal += $subtotal;

                $validatedItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $item['qty'],
                    'notes' => $item['notes']
                ];
            }

            // Buat transaksi QR baru
            $trxNumber = 'QR-' . date('YmdHis') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            $customerName = trim($request->customer_name) ?: 'Pelanggan Meja ' . $table->name;

            $transaction = Transaction::create([
                'user_id' => null, // Pelanggan self-order tanpa login kasir
                'transaction_number' => $trxNumber,
                'customer_name' => $customerName,
                'table_number' => $table->name,
                'order_source' => 'qr',
                'order_status' => 'pending_payment',
                'payment_status' => 'pending',
                'total' => $calculatedTotal,
                'payment' => 0,
                'change' => 0,
                'status' => 'unpaid',
                'payment_method' => $request->payment_method ?: 'QRIS',
                'discount' => 0
            ]);

            // Simpan detail transaksi
            foreach ($validatedItems as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'qty_printed' => 0,
                    'price' => $item['price'],
                    'notes' => $item['notes']
                ]);
            }

            // Pastikan sesi meja aktif tercatat dengan nama pelanggan
            $session = OrderSession::firstOrCreate(
                ['dining_table_id' => $table->id, 'status' => 'active'],
                ['customer_name' => $customerName, 'session_token' => Str::random(40)]
            );
            $session->update(['customer_name' => $customerName]);

            // Inisiasi Payment Gateway
            $paymentResult = $this->paymentService->createPayment($transaction, [
                'payment_method' => $request->payment_method ?: 'QRIS'
            ]);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['message'] ?? 'Gagal membuat tagihan pembayaran.');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'transaction_number' => $transaction->transaction_number,
                'payment_url' => $paymentResult['payment_url'] ?? '',
                'snap_token' => $paymentResult['token'] ?? '',
                'gateway' => $this->paymentService->getGateway()->getName(),
                'status_url' => url('/order/status/' . $transaction->transaction_number)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Simulator hanya tersedia jika gateway aktif = mock DAN mock diizinkan (bukan production).
     * Di production route ini 404 sehingga tidak ada jalan "bayar" tanpa uang.
     */
    private function ensureMockSimulatorEnabled(): void
    {
        $isMockActive = $this->paymentService->getGateway()->getName() === 'mock';
        abort_unless($isMockActive && \App\Services\Payment\MockPaymentGateway::isAllowed(), 404);
    }

    /**
     * Halaman Simulator Pembayaran (Mock Gateway untuk Uji Coba Offline/Lokal)
     */
    public function paymentSimulator($transaction_number, Request $request)
    {
        $this->ensureMockSimulatorEnabled();

        $transaction = Transaction::with(['details.product'])
            ->where('transaction_number', $transaction_number)
            ->firstOrFail();

        return view('customer.payment_simulator', compact('transaction'));
    }

    /**
     * Proses Bayar pada Mock Simulator
     */
    public function processMockPayment($transaction_number, Request $request)
    {
        $this->ensureMockSimulatorEnabled();
        $status = $request->input('action') === 'fail' ? 'failed' : 'paid';
        $method = $request->input('payment_method', 'QRIS');

        // Panggil service webhook secara internal
        $mockRequest = new Request([
            'transaction_number' => $transaction_number,
            'status' => $status,
            'payment_method' => $method,
            'external_id' => 'MOCK-' . time(),
        ]);

        $this->paymentService->handleWebhook($mockRequest);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('customer.order_status', $transaction_number)
            ]);
        }

        return redirect()->route('customer.order_status', $transaction_number);
    }

    /**
     * Halaman Pelacakan Status Pesanan Pelanggan
     */
    public function orderStatus($transaction_number)
    {
        $transaction = Transaction::with(['details.product', 'diningTable'])
            ->where('transaction_number', $transaction_number)
            ->firstOrFail();

        $table = DiningTable::where('name', $transaction->table_number)->first();

        return view('customer.status', compact('transaction', 'table'));
    }

    /**
     * Polling Status Transaksi Pelanggan secara Realtime (JSON)
     */
    public function checkStatus($transaction_number)
    {
        $transaction = Transaction::where('transaction_number', $transaction_number)->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'transaction_number' => $transaction->transaction_number,
            'order_status' => $transaction->order_status,
            'payment_status' => $transaction->payment_status,
            'status' => $transaction->status,
            'is_paid' => $transaction->isPaid(),
            'total' => $transaction->total,
            'formatted_total' => 'Rp ' . number_format($transaction->total, 0, ',', '.')
        ]);
    }
}
