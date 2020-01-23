<?php

return [
    'test_mode' => env('XEPAY_TEST', false),

    'default' => [
        'provider' => env('XEPAY_PROVIDER'),
    ],

    'enables' => env('XEPAY_ENABLES', 'paypal'),

    'drivers' => [
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
