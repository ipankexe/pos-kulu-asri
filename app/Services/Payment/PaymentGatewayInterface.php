<?php

namespace App\Services\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Inisiasi permintaan pembayaran ke Payment Gateway
     *
     * @param Transaction $transaction
     * @param array $params
     * @return array ['success' => bool, 'payment_url' => string, 'token' => string, 'external_id' => string, 'raw' => array]
     */
    public function createPayment(Transaction $transaction, array $params = []): array;

    /**
     * Verifikasi webhook callback dari Payment Gateway
     *
     * @param Request $request
     * @return array ['success' => bool, 'transaction_number' => string, 'status' => string, 'external_id' => string, 'raw' => array]
     */
    public function verifyWebhook(Request $request): array;

    /**
     * Batalkan pembayaran
     *
     * @param string $externalId
     * @return bool
     */
    public function cancelPayment(string $externalId): bool;

    /**
     * Nama gateway
     */
    public function getName(): string;
}
