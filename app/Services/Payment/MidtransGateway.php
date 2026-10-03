<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransGateway implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('payment.gateways.midtrans.server_key', env('PAYMENT_SERVER_KEY', ''));
        $this->clientKey = config('payment.gateways.midtrans.client_key', env('PAYMENT_CLIENT_KEY', ''));
        $this->isProduction = (bool) config('payment.gateways.midtrans.is_production', false);
    }

    public function getName(): string
    {
        return 'midtrans';
    }

    public function createPayment(Transaction $transaction, array $params = []): array
    {
        $baseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $items = [];
        foreach ($transaction->details as $detail) {
            $items[] = [
                'id' => (string) $detail->product_id,
                'price' => (int) $detail->price,
                'quantity' => (int) $detail->qty,
                'name' => mb_substr($detail->product->name ?? ('Item #' . $detail->product_id), 0, 50),
            ];
        }

        if ($transaction->discount > 0) {
            $items[] = [
                'id' => 'DISCOUNT',
                'price' => -(int) $transaction->discount,
                'quantity' => 1,
                'name' => 'Diskon Promo',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_number,
                'gross_amount' => (int) $transaction->total,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name ?: 'Pelanggan Kulu Asri',
            ],
            'item_details' => $items,
            'callbacks' => [
                'finish' => url('/order/status/' . $transaction->transaction_number),
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($baseUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'payment_url' => $data['redirect_url'] ?? '',
                    'token' => $data['token'] ?? '',
                    'external_id' => $transaction->transaction_number,
                    'raw' => $data,
                ];
            }

            Log::error('Midtrans Snap Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Gagal membuat tagihan ke Midtrans: ' . ($response->json()['error_messages'][0] ?? 'Terjadi kesalahan gateway'),
                'payment_url' => '',
                'token' => '',
                'external_id' => '',
                'raw' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi ke Payment Gateway terputus: ' . $e->getMessage(),
                'payment_url' => '',
                'token' => '',
                'external_id' => '',
                'raw' => [],
            ];
        }
    }

    public function verifyWebhook(Request $request): array
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? '';
        $statusCode = $payload['status_code'] ?? '';
        $grossAmount = $payload['gross_amount'] ?? '';
        $signatureKey = $payload['signature_key'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';
        $paymentType = $payload['payment_type'] ?? 'midtrans';

        // Validasi signature
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        if ($signatureKey !== $expectedSignature) {
            Log::warning("Midtrans Webhook: Invalid signature for Order #{$orderId}");
            return [
                'success' => false,
                'message' => 'Invalid signature key',
                'transaction_number' => $orderId,
                'status' => 'invalid',
                'external_id' => $payload['transaction_id'] ?? '',
                'payment_method' => $paymentType,
                'raw' => $payload
            ];
        }

        $mappedStatus = 'pending';
        if ($transactionStatus === 'capture') {
            $mappedStatus = ($fraudStatus === 'accept') ? 'paid' : 'pending';
        } elseif ($transactionStatus === 'settlement') {
            $mappedStatus = 'paid';
        } elseif ($transactionStatus === 'pending') {
            $mappedStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'cancel'])) {
            $mappedStatus = 'failed';
        } elseif ($transactionStatus === 'expire') {
            $mappedStatus = 'expired';
        }

        return [
            'success' => true,
            'transaction_number' => $orderId,
            'status' => $mappedStatus,
            'external_id' => $payload['transaction_id'] ?? $orderId,
            'payment_method' => $paymentType,
            'raw' => $payload
        ];
    }

    public function cancelPayment(string $externalId): bool
    {
        $baseUrl = $this->isProduction
            ? "https://api.midtrans.com/v2/{$externalId}/cancel"
            : "https://api.sandbox.midtrans.com/v2/{$externalId}/cancel";

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($baseUrl);
            return $res->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
