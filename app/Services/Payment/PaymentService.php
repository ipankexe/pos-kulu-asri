<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;

    public function __construct()
    {
        $defaultGateway = config('payment.default', 'mock');
        $this->gateway = $this->resolveGateway($defaultGateway);
    }

    public function resolveGateway(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'midtrans' => new MidtransGateway(),
            default => new MockPaymentGateway(),
        };
    }

    public function setGateway(PaymentGatewayInterface $gateway): self
    {
        $this->gateway = $gateway;
        return $this;
    }

    public function getGateway(): PaymentGatewayInterface
    {
        return $this->gateway;
    }

    /**
     * Inisiasi tagihan pembayaran untuk transaksi
     */
    public function createPayment(Transaction $transaction, array $params = []): array
    {
        $result = $this->gateway->createPayment($transaction, $params);

        if ($result['success']) {
            $transaction->update([
                'payment_gateway' => $this->gateway->getName(),
                'payment_reference' => $result['token'] ?? $result['payment_url'],
                'external_payment_id' => $result['external_id'] ?? null,
                'payment_status' => 'pending',
                'order_status' => 'pending_payment',
            ]);

            Payment::create([
                'transaction_id' => $transaction->id,
                'payment_gateway' => $this->gateway->getName(),
                'payment_method' => $params['payment_method'] ?? 'Online Payment',
                'external_id' => $result['external_id'] ?? $transaction->transaction_number,
                'amount' => $transaction->total,
                'status' => 'pending',
                'raw_response' => $result['raw'] ?? [],
            ]);
        }

        return $result;
    }

    /**
     * Memproses Webhook Payment Gateway secara Idempoten dan Aman
     */
    public function handleWebhook(Request $request): array
    {
        $verifyResult = $this->gateway->verifyWebhook($request);

        if (!$verifyResult['success']) {
            Log::warning("Payment webhook verification failed: " . json_encode($verifyResult));
            return $verifyResult;
        }

        $trxNumber = $verifyResult['transaction_number'];
        $status = $verifyResult['status']; // 'paid', 'pending', 'failed', 'expired', 'cancelled'
        $method = $verifyResult['payment_method'] ?? 'Online Payment';
        $externalId = $verifyResult['external_id'] ?? null;

        DB::beginTransaction();
        try {
            // Lock baris transaksi untuk mencegah race condition (Idempotency Lock)
            $transaction = Transaction::with('details')->where('transaction_number', $trxNumber)->lockForUpdate()->first();

            if (!$transaction) {
                DB::rollBack();
                Log::error("Payment Webhook: Transaction #{$trxNumber} not found.");
                return ['success' => false, 'message' => "Transaction #{$trxNumber} not found."];
            }

            // IDEMPOTENCY CHECK: Jika transaksi sudah terbayar (paid), jangan potong stok dua kali!
            if ($transaction->payment_status === 'paid' || $transaction->status === 'paid') {
                DB::commit();
                Log::info("Payment Webhook: Transaction #{$trxNumber} already marked as paid. Ignoring duplicate webhook.");
                return [
                    'success' => true,
                    'message' => 'Transaction already processed (Idempotent)',
                    'transaction' => $transaction
                ];
            }

            $payment = Payment::where('transaction_id', $transaction->id)->latest()->first();

            if ($status === 'paid') {
                // 1. Potong stok produk secara atomik
                foreach ($transaction->details as $detail) {
                    $product = Product::where('id', $detail->product_id)->lockForUpdate()->first();
                    if ($product) {
                        // Jika stok kurang dari qty yang dipesan, tetap kurangi atau catat
                        $product->decrement('stock', $detail->qty);
                        StockLog::create([
                            'product_id' => $product->id,
                            'qty_out' => $detail->qty
                        ]);
                    }
                }

                // 2. Update status transaksi
                $transaction->update([
                    'status' => 'paid',
                    'order_status' => 'confirmed', // masuk antrean dapur/kasir
                    'payment_status' => 'paid',
                    'payment' => $transaction->total,
                    'change' => 0,
                    'payment_method' => $method ?: 'QRIS',
                    'external_payment_id' => $externalId ?: $transaction->external_payment_id,
                ]);

                // 3. Update status meja menjadi 'occupied'
                $table = DiningTable::where('name', $transaction->table_number)->first();
                if ($table && $table->status !== 'occupied') {
                    $table->update(['status' => 'occupied']);
                }

                // 4. Update log payment
                if ($payment) {
                    $payment->update([
                        'status' => 'paid',
                        'payment_method' => $method ?: $payment->payment_method,
                        'external_id' => $externalId ?: $payment->external_id,
                        'paid_at' => now(),
                        'raw_response' => $verifyResult['raw'] ?? $payment->raw_response
                    ]);
                }
            } elseif (in_array($status, ['failed', 'expired', 'cancelled'])) {
                $transaction->update([
                    'order_status' => 'cancelled',
                    'payment_status' => $status,
                ]);

                if ($payment) {
                    $payment->update([
                        'status' => $status,
                        'raw_response' => $verifyResult['raw'] ?? $payment->raw_response
                    ]);
                }
            }

            DB::commit();
            return [
                'success' => true,
                'status' => $status,
                'transaction' => $transaction
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment Webhook Exception for #{$trxNumber}: " . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
