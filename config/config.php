<?php

return [
    'test_mode' => env('XEPAY_TEST', false),

    'default' => [
//        'merchant' => env('PAYMENT_MERCHANT', 'paypal'),
        'provider' => env('PAYMENT_PROVIDER'),
    ],

    'enables' => env('PAYMENT_ENABLES', 'paypal'),

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
    ],

    'route' => 'xepay',
];
