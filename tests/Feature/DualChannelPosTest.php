<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\Models\User;
use App\Models\DiningTable;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\OrderSession;
use App\Models\Payment;
use App\Models\StockLog;
use App\Services\Payment\PaymentService;
use PHPUnit\Framework\Attributes\Test;

class DualChannelPosTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $cashierUser;
    protected DiningTable $table1;
    protected Category $category;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Kulu Asri',
            'role' => 'admin',
        ]);

        $this->cashierUser = User::factory()->create([
            'name' => 'Kasir Kulu Asri',
            'role' => 'kasir',
        ]);

        $this->table1 = DiningTable::create([
            'name' => 'Meja 05',
            'status' => 'available',
            'is_active' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Makanan Utama',
        ]);

        $this->product1 = Product::create([
            'name' => 'Ayam Bakar Madu',
            'category_id' => $this->category->id,
            'price' => 25000,
            'cost_price' => 18000,
            'stock' => 20,
        ]);

        $this->product2 = Product::create([
            'name' => 'Es Teh Manis',
            'category_id' => $this->category->id,
            'price' => 5000,
            'cost_price' => 2000,
            'stock' => 10,
        ]);
    }

    #[Test]
    public function customer_can_access_menu_with_valid_qr_token()
    {
        $response = $this->get(route('customer.table_order', $this->table1->qr_token));

        $response->assertStatus(200);
        $response->assertSee('Meja 05');
        $response->assertSee('Ayam Bakar Madu');
    }

    #[Test]
    public function customer_gets_error_page_for_invalid_qr_token()
    {
        $response = $this->get(route('customer.table_order', 'invalid-qr-token-123'));

        $response->assertStatus(404);
        $response->assertSee('QR Code Tidak Dikenali');
    }

    #[Test]
    public function customer_gets_error_when_table_is_inactive()
    {
        $this->table1->update(['is_active' => false]);

        $response = $this->get(route('customer.table_order', $this->table1->qr_token));

        $response->assertStatus(403);
        $response->assertSee('Meja Sedang Tidak Aktif');
    }

    #[Test]
    public function menu_data_api_returns_products_and_table_details()
    {
        $response = $this->getJson(route('customer.menu_data', $this->table1->qr_token));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'table' => [
                'name' => 'Meja 05',
            ],
        ]);
        $response->assertJsonFragment(['name' => 'Ayam Bakar Madu']);
    }

    #[Test]
    public function customer_checkout_validates_stock_sufficiency()
    {
        $payload = [
            'customer_name' => 'Budi',
            'payment_method' => 'QRIS',
            'cart' => [
                [
                    'id' => $this->product1->id,
                    'qty' => 25, // Stock is only 20
                    'notes' => 'Tidak pedas',
                ]
            ]
        ];

        $response = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $response->assertJsonFragment(['message' => 'Stok Ayam Bakar Madu tidak mencukupi (sisa: 20).']);
    }

    #[Test]
    public function customer_checkout_calculates_server_price_and_creates_pending_qr_order()
    {
        $payload = [
            'customer_name' => 'Pak Joko',
            'payment_method' => 'QRIS',
            'cart' => [
                [
                    'id' => $this->product1->id, // 25,000 * 2 = 50,000
                    'qty' => 2,
                    'notes' => 'Paha semua',
                ],
                [
                    'id' => $this->product2->id, // 5,000 * 3 = 15,000
                    'qty' => 3,
                    'notes' => 'Manis sedang',
                ],
            ]
        ];

        $response = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'transaction_number',
            'payment_url',
            'status_url',
        ]);

        $trxNumber = $response->json('transaction_number');
        $this->assertNotNull($trxNumber);

        // Verify transaction record in database
        $transaction = Transaction::where('transaction_number', $trxNumber)->first();
        $this->assertNotNull($transaction);
        $this->assertEquals('qr', $transaction->order_source);
        $this->assertEquals('pending_payment', $transaction->order_status);
        $this->assertEquals('pending', $transaction->payment_status);
        $this->assertNull($transaction->user_id);
        $this->assertEquals('Pak Joko', $transaction->customer_name);
        $this->assertEquals('Meja 05', $transaction->table_number);
        // Correct total: 50,000 + 15,000 = 65,000
        $this->assertEquals(65000, $transaction->total);

        // Verify stock has NOT been deducted yet (payment is still pending)
        $this->assertEquals(20, $this->product1->fresh()->stock);
        $this->assertEquals(10, $this->product2->fresh()->stock);

        // Verify order session created/updated
        $session = OrderSession::where('dining_table_id', $this->table1->id)->where('status', 'active')->first();
        $this->assertNotNull($session);
        $this->assertEquals('Pak Joko', $session->customer_name);
    }

    #[Test]
    public function payment_simulation_atomically_deducts_stock_and_updates_status()
    {
        // 1. Create a pending QR order
        $payload = [
            'customer_name' => 'Siti Aminah',
            'payment_method' => 'QRIS',
            'cart' => [
                [
                    'id' => $this->product1->id,
                    'qty' => 3, // 3 * 25,000 = 75,000
                    'notes' => '',
                ]
            ]
        ];
        $checkoutRes = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);
        $trxNumber = $checkoutRes->json('transaction_number');

        $transaction = Transaction::where('transaction_number', $trxNumber)->first();
        $this->assertEquals(20, $this->product1->fresh()->stock);

        // 2. Process mock payment simulator
        $payRes = $this->postJson(route('customer.mock_payment.process', $trxNumber), [
            'action' => 'pay',
            'payment_method' => 'QRIS',
        ]);
        $payRes->assertStatus(200);
        $payRes->assertJson(['success' => true]);

        // 3. Verify stock is atomically deducted
        $this->assertEquals(17, $this->product1->fresh()->stock); // 20 - 3 = 17

        // 4. Verify StockLog is recorded
        $stockLog = StockLog::where('product_id', $this->product1->id)->latest()->first();
        $this->assertNotNull($stockLog);
        $this->assertEquals(3, $stockLog->qty_out);

        // 5. Verify Transaction updated
        $transaction->refresh();
        $this->assertEquals('paid', $transaction->payment_status);
        $this->assertEquals('paid', $transaction->status);
        $this->assertEquals('confirmed', $transaction->order_status);

        // 6. Verify Payment entry created
        $payment = Payment::where('transaction_id', $transaction->id)->latest()->first();
        $this->assertNotNull($payment);
        $this->assertEquals('paid', $payment->status);
        $this->assertEquals(75000, $payment->amount);
    }

    #[Test]
    public function payment_processing_is_idempotent_and_prevents_duplicate_stock_deduction()
    {
        $payload = [
            'customer_name' => 'Rian',
            'payment_method' => 'QRIS',
            'cart' => [
                [
                    'id' => $this->product2->id, // stock 10
                    'qty' => 4,
                    'notes' => '',
                ]
            ]
        ];
        $checkoutRes = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);
        $trxNumber = $checkoutRes->json('transaction_number');

        $paymentService = app(PaymentService::class);

        // First payment delivery
        $request1 = new Request([
            'transaction_number' => $trxNumber,
            'status' => 'paid',
            'payment_method' => 'QRIS',
            'external_id' => 'EXT-IDEMPOTENT-001'
        ]);
        $res1 = $paymentService->handleWebhook($request1);
        $this->assertTrue($res1['success']);
        $this->assertEquals(6, $this->product2->fresh()->stock); // 10 - 4 = 6

        // Duplicate payment delivery (same transaction received again)
        $request2 = new Request([
            'transaction_number' => $trxNumber,
            'status' => 'paid',
            'payment_method' => 'QRIS',
            'external_id' => 'EXT-IDEMPOTENT-001'
        ]);
        $res2 = $paymentService->handleWebhook($request2);
        $this->assertTrue($res2['success']); // Returns true (acknowledged)

        // Stock MUST REMAIN 6, not decremented twice to 2!
        $this->assertEquals(6, $this->product2->fresh()->stock);

        // Stock logs must only have 1 entry
        $logsCount = StockLog::where('product_id', $this->product2->id)->count();
        $this->assertEquals(1, $logsCount);
    }

    #[Test]
    public function order_status_check_endpoint_returns_live_status()
    {
        $payload = [
            'customer_name' => 'Dewi',
            'payment_method' => 'QRIS',
            'cart' => [
                ['id' => $this->product1->id, 'qty' => 1]
            ]
        ];
        $checkoutRes = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);
        $trxNumber = $checkoutRes->json('transaction_number');

        $response = $this->getJson(route('customer.check_status', $trxNumber));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'payment_status' => 'pending',
            'order_status' => 'pending_payment',
        ]);
    }

    #[Test]
    public function cashier_can_see_incoming_qr_orders_and_update_status()
    {
        // 1. Create a paid QR order
        $payload = [
            'customer_name' => 'Meja 5 Tamu',
            'payment_method' => 'QRIS',
            'cart' => [
                ['id' => $this->product1->id, 'qty' => 2]
            ]
        ];
        $checkoutRes = $this->postJson(route('customer.checkout', $this->table1->qr_token), $payload);
        $trxNumber = $checkoutRes->json('transaction_number');

        // Simulate payment completion
        $this->postJson(route('customer.mock_payment.process', $trxNumber), [
            'action' => 'pay',
            'payment_method' => 'QRIS',
        ]);

        $trx = Transaction::where('transaction_number', $trxNumber)->first();

        // 2. Cashier checks incoming QR orders
        $response = $this->actingAs($this->cashierUser)->getJson(route('pos.incoming_qr'));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $orders = $response->json('orders');
        $this->assertNotEmpty($orders);
        $this->assertEquals($trxNumber, $orders[0]['transaction_number']);

        // 3. Cashier confirms order for kitchen (status -> preparing)
        $confirmRes = $this->actingAs($this->cashierUser)->postJson(route('pos.confirm_qr', $trx->id));
        $confirmRes->assertStatus(200);
        $this->assertEquals('preparing', $trx->fresh()->order_status);

        // 4. Kitchen marks order as ready
        $statusRes = $this->actingAs($this->cashierUser)->postJson(route('pos.update_status', $trx->id), [
            'status' => 'ready'
        ]);
        $statusRes->assertStatus(200);
        $this->assertEquals('ready', $trx->fresh()->order_status);
    }

    #[Test]
    public function existing_pos_cashier_order_continues_to_function_with_pos_channel()
    {
        $payload = [
            'cart' => [
                [
                    'id' => $this->product1->id,
                    'qty' => 1,
                    'notes' => 'Bungkus',
                ]
            ],
            'table_number' => 'Takeaway',
            'customer_name' => 'Pelanggan Kasir Direct',
        ];

        // 1. Save order at POS
        $response = $this->actingAs($this->cashierUser)->postJson(route('pos.save_order'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $trxId = $response->json('transaction_id');
        $trx = Transaction::find($trxId);
        $this->assertNotNull($trx);
        $this->assertEquals('pos', $trx->order_source);
        $this->assertEquals($this->cashierUser->id, $trx->user_id);
        $this->assertEquals('unpaid', $trx->status);

        // 2. Pay order at POS
        $payRes = $this->actingAs($this->cashierUser)->postJson(route('pos.pay_order', $trxId), [
            'payment_method' => 'Cash',
            'payment' => 30000,
            'change' => 5000,
            'discount' => 0,
        ]);
        $payRes->assertStatus(200);
        $payRes->assertJson(['success' => true]);

        $trx->refresh();
        $this->assertEquals('paid', $trx->status);
        $this->assertEquals('completed', $trx->order_status);
    }

    #[Test]
    public function admin_can_filter_reports_by_channel_and_view_pdf()
    {
        // Create 1 POS transaction and 1 QR transaction
        Transaction::create([
            'transaction_number' => 'TRX-POS-001',
            'user_id' => $this->cashierUser->id,
            'order_source' => 'pos',
            'table_number' => 'Meja 1',
            'customer_name' => 'User POS',
            'total' => 30000,
            'payment' => 30000,
            'change' => 0,
            'discount' => 0,
            'status' => 'paid',
            'payment_method' => 'Cash',
        ]);

        Transaction::create([
            'transaction_number' => 'TRX-QR-002',
            'user_id' => null,
            'order_source' => 'qr',
            'table_number' => 'Meja 2',
            'customer_name' => 'User QR',
            'total' => 45000,
            'payment' => 45000,
            'change' => 0,
            'discount' => 0,
            'status' => 'paid',
            'order_status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'QRIS',
        ]);

        // Filter by POS
        $posReport = $this->actingAs($this->adminUser)->get(route('admin.reports', ['source' => 'pos']));
        $posReport->assertStatus(200);
        $posReport->assertSee('TRX-POS-001');
        $posReport->assertDontSee('TRX-QR-002');

        // Filter by QR
        $qrReport = $this->actingAs($this->adminUser)->get(route('admin.reports', ['source' => 'qr']));
        $qrReport->assertStatus(200);
        $qrReport->assertSee('TRX-QR-002');
        $qrReport->assertDontSee('TRX-POS-001');

        // Export PDF
        $pdfRes = $this->actingAs($this->adminUser)->get(route('admin.reports.pdf', ['source' => 'all']));
        $pdfRes->assertStatus(200);
        $pdfRes->assertSee('TRX-POS-001');
        $pdfRes->assertSee('TRX-QR-002');
        $pdfRes->assertSee('Kanal');
    }

    #[Test]
    public function cashier_can_track_today_income_and_source_breakdown()
    {
        // 1. Create 1 POS Cash transaction today
        $posTrx = Transaction::create([
            'transaction_number' => 'TRX-POS-TODAY-01',
            'user_id' => $this->cashierUser->id,
            'order_source' => 'pos',
            'table_number' => 'Meja 1',
            'customer_name' => 'Budi Santoso',
            'total' => 50000,
            'payment' => 50000,
            'change' => 0,
            'discount' => 0,
            'status' => 'paid',
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'payment_method' => 'Cash',
        ]);

        // 2. Create 1 QR QRIS transaction today
        $qrTrx = Transaction::create([
            'transaction_number' => 'TRX-QR-TODAY-02',
            'user_id' => null,
            'order_source' => 'qr',
            'table_number' => 'Meja 2',
            'customer_name' => 'Siti Rahma',
            'total' => 75000,
            'payment' => 75000,
            'change' => 0,
            'discount' => 0,
            'status' => 'paid',
            'payment_status' => 'paid',
            'order_status' => 'ready',
            'payment_method' => 'QRIS',
        ]);

        // 3. Hit today-income API as cashier
        $response = $this->actingAs($this->cashierUser)->getJson(route('pos.today_income'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'summary' => [
                'total_revenue' => 125000,
                'count' => 2,
                'pos_revenue' => 50000,
                'pos_count' => 1,
                'qr_revenue' => 75000,
                'qr_count' => 1,
                'cash_total' => 50000,
                'cash_count' => 1,
                'qris_total' => 75000,
                'qris_count' => 1,
            ]
        ]);

        $transactions = collect($response->json('transactions'));
        $this->assertTrue($transactions->contains('transaction_number', 'TRX-POS-TODAY-01'));
        $this->assertTrue($transactions->contains('transaction_number', 'TRX-QR-TODAY-02'));

        // 4. Visit POS index page as cashier and verify 4 center buttons
        $posIndexRes = $this->actingAs($this->cashierUser)->get(route('pos.index'));
        $posIndexRes->assertStatus(200);
        $posIndexRes->assertSee('Pesanan Baru');
        $posIndexRes->assertSee('Kasir / Pembayaran');
        $posIndexRes->assertSee('Pesanan QR');
        $posIndexRes->assertSee('Pendapatan Hari Ini');

        // 5. Visit history page as cashier
        $historyRes = $this->actingAs($this->cashierUser)->get(route('pos.history'));
        $historyRes->assertStatus(200);
        $historyRes->assertSee('TOTAL OMSET HARI INI');
        $historyRes->assertSee('DARI POS KASIR');
        $historyRes->assertSee('DARI QR SELF-ORDER');
        $historyRes->assertSee('TRX-POS-TODAY-01');
        $historyRes->assertSee('TRX-QR-TODAY-02');
    }
}
