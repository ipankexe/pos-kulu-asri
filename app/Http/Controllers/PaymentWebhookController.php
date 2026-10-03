<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Payment\PaymentService;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Endpoint Webhook Payment Gateway (Midtrans, Xendit, Simulator)
     */
    public function handleWebhook(Request $request)
    {
        Log::info("Payment Webhook Received: ", $request->all());

        $result = $this->paymentService->handleWebhook($request);

        if (!$result['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Failed to process webhook'
            ], 400);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Payment webhook processed successfully'
        ], 200);
    }
}
