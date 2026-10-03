<?php

return [
    'default' => env('PAYMENT_GATEWAY', 'mock'), // 'mock' or 'midtrans' or 'xendit'

    // Keamanan: gateway simulator (mock) TIDAK memungut uang sungguhan.
    // Default-nya hanya diizinkan di luar production (local/testing).
    // Jika APP_ENV=production tapi PAYMENT_GATEWAY masih 'mock', pembayaran online otomatis ditolak.
    'allow_mock' => (bool) env('PAYMENT_ALLOW_MOCK', env('APP_ENV', 'production') !== 'production'),

    'gateways' => [
        'midtrans' => [
            'server_key' => env('PAYMENT_SERVER_KEY', ''),
            'client_key' => env('PAYMENT_CLIENT_KEY', ''),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
            'is_sanitized' => true,
            'is_3ds' => true,
        ],
        'mock' => [
            'name' => 'Simulator Kulu Asri Pay',
        ]
    ]
];
