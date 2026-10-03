<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MockPaymentGateway implements PaymentGatewayInterface
{
    public function getName(): string
    {
        return 'mock';
    }

    /**
     * Mock hanya boleh aktif di lingkungan non-production (lihat config payment.allow_mock).
     */
    public static function isAllowed(): bool
    {
        return (bool) config('payment.allow_mock', false);
    }

    public function createPayment(Transaction $transaction, array $params = []): array
    {
        if (!self::isAllowed()) {
            return [
                'success' => false,
                'message' => 'Pembayaran online belum dikonfigurasi. Silakan pesan dan bayar langsung di kasir.',
                'payment_url' => '',
                'token' => '',
                'external_id' => '',
                'raw' => [],
            ];
        }

        $token = 'MOCK-' . Str::random(24);
        $externalId = 'MOCK-TRX-' . time() . '-' . rand(100, 999);

        // Simulator URL
        $paymentUrl = url('/order/payment/simulator/' . $transaction->transaction_number . '?token=' . $token);

        return [
            'success' => true,
            'payment_url' => $paymentUrl,
            'token' => $token,
            'external_id' => $externalId,
            'raw' => [
                'gateway' => 'mock',
                'order_id' => $transaction->transaction_number,
                'amount' => $transaction->total,
                'token' => $token
            ]
        ];
    }

    public function verifyWebhook(Request $request): array
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? $payload['transaction_number'] ?? '';

        // Tanpa signature, webhook mock bisa dipalsukan siapa saja → tolak di production.
        if (!self::isAllowed()) {
            return [
                'success' => false,
                'message' => 'Mock payment gateway is disabled in this environment.',
                'transaction_number' => $orderId,
                'status' => 'invalid',
            ];
        }

        $status = $payload['status'] ?? 'paid'; // 'paid', 'failed', 'expired'
        $method = $payload['payment_method'] ?? 'QRIS';

        return [
            'success' => true,
            'transaction_number' => $orderId,
            'status' => in_array($status, ['paid', 'failed', 'expired', 'cancelled']) ? $status : 'paid',
            'external_id' => $payload['external_id'] ?? ('MOCK-TRX-' . time()),
            'payment_method' => $method,
            'raw' => $payload
        ];
    }

    public function cancelPayment(string $externalId): bool
    {
        return true;
    }
}
