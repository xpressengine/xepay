<?php

return [
    'default' => [
        'merchant' => env('PAYMENT_MERCHANT', 'paypal'),
        'provider' => env('PAYMENT_PROVIDER'),
    ],

    'merchants' => [
        'paypal' => [
            'debug' => env('PAYPAL_DEBUG', false),
            'id' => env('PAYPAL_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'log_enabled' => true,
            'log_path' => storage_path('logs/paypal.log'),
            'cache_enabled' => true,
            'cache_path' => storage_path('framework/cache/paypal/paypal.cache'),
        ],
        'test' => [
            // no need config
        ],
    ],
];
