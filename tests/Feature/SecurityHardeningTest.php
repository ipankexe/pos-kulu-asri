<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\DiningTable;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;

/**
 * Regression tests for the security fixes (K1–K4) and the login redirect.
 */
class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $cashierUser;
    protected DiningTable $table;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->cashierUser = User::factory()->create(['role' => 'kasir']);

        $this->table = DiningTable::create([
            'name' => 'Meja 07',
            'status' => 'available',
            'is_active' => true,
        ]);

        $category = Category::create(['name' => 'Ikan Bakar']);
        $this->product = Product::create([
            'name' => 'Gurame Bakar',
            'category_id' => $category->id,
            'price' => 50000,
            'cost_price' => 30000,
            'stock' => 10,
        ]);
    }

    private function createPendingQrOrder(): Transaction
    {
        return Transaction::create([
            'transaction_number' => 'QR-TEST-0001',
            'user_id' => null,
            'customer_name' => 'Iseng',
            'table_number' => $this->table->name,
            'order_source' => 'qr',
            'order_status' => 'pending_payment',
            'payment_status' => 'pending',
            'total' => 50000,
            'payment' => 0,
            'change' => 0,
            'status' => 'unpaid',
            'payment_method' => 'QRIS',
            'discount' => 0,
        ]);
    }

    // ---------- K2: token QR tidak bisa ditebak ----------

    #[Test]
    public function table_menu_cannot_be_opened_by_guessing_id_name_or_token_prefix()
    {
        $guesses = [
            (string) $this->table->id,                 // ID meja
            'meja-07',                                 // nama meja (slug)
            'Meja 07',                                 // nama meja asli
            substr($this->table->qr_token, 0, 2),      // potongan token
        ];

        foreach ($guesses as $guess) {
            $this->get('/order/table/' . rawurlencode($guess))
                ->assertStatus(404)
                ->assertSee('QR Code Tidak Dikenali');
        }

        // Token asli tetap bisa
        $this->get(route('customer.table_order', $this->table->qr_token))->assertStatus(200);
    }

    // ---------- K3: daftar meja tidak publik ----------

    #[Test]
    public function table_list_with_tokens_requires_login()
    {
        $this->get(route('customer.tables_list'))->assertRedirect(route('login'));

        $this->actingAs($this->adminUser)
            ->get(route('customer.tables_list'))
            ->assertStatus(200);
    }

    // ---------- K1: tidak bisa "bayar" tanpa uang ----------

    #[Test]
    public function mock_simulator_and_webhook_are_disabled_when_mock_not_allowed()
    {
        config(['payment.default' => 'mock', 'payment.allow_mock' => false]);
        $trx = $this->createPendingQrOrder();

        $this->get(route('customer.mock_payment.show', $trx->transaction_number))->assertStatus(404);
        $this->post(route('customer.mock_payment.process', $trx->transaction_number), ['action' => 'pay'])
            ->assertStatus(404);

        // Webhook publik tanpa signature juga harus ditolak
        $this->postJson(route('payment.webhook'), [
            'transaction_number' => $trx->transaction_number,
            'status' => 'paid',
        ]);

        $trx->refresh();
        $this->assertEquals('unpaid', $trx->status);
        $this->assertEquals('pending', $trx->payment_status);
        $this->assertEquals(10, $this->product->fresh()->stock);
    }

    #[Test]
    public function qr_checkout_is_refused_when_mock_gateway_not_allowed()
    {
        config(['payment.default' => 'mock', 'payment.allow_mock' => false]);

        $this->postJson(route('customer.checkout', $this->table->qr_token), [
            'customer_name' => 'Budi',
            'cart' => [['id' => $this->product->id, 'qty' => 1]],
        ])->assertStatus(422)->assertJson(['success' => false]);

        $this->assertEquals(0, Transaction::count());
    }

    #[Test]
    public function mock_simulator_is_unavailable_when_real_gateway_is_active()
    {
        config(['payment.default' => 'midtrans', 'payment.allow_mock' => true]);
        $trx = $this->createPendingQrOrder();

        $this->post(route('customer.mock_payment.process', $trx->transaction_number), ['action' => 'pay'])
            ->assertStatus(404);

        $this->assertEquals('unpaid', $trx->fresh()->status);
    }

    // ---------- K4: anti-XSS helper tersedia di layar kasir ----------

    #[Test]
    public function cashier_screen_ships_html_escaping_helper()
    {
        $this->actingAs($this->cashierUser)
            ->get(route('pos.index'))
            ->assertStatus(200)
            ->assertSee('function esc(value)', false)
            ->assertSee('Pemesan: ${esc(order.customer_name', false);
    }

    // ---------- Login selalu ke /home ----------

    #[Test]
    public function login_always_redirects_to_home_instead_of_intended_url()
    {
        // Tamu mencoba buka /pos dulu → Laravel menyimpan URL "intended"
        $this->get(route('pos.index'))->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $this->adminUser->email,
            'password' => 'password',
        ])->assertRedirect(route('home'));

        // /home lalu mengarahkan sesuai role
        $this->get(route('home'))->assertRedirect(route('admin.dashboard'));
    }
}
