<?php

return [
    'rules' => [
        'deposit' => [
            'default' => env('DEFAULT_DEPOSIT_COMMISSION', 0.03),
        ],
        'withdraw' => [
            'default' => env('DEFAULT_WITHDRAW_COMMISSION', 0.3),
            'business' => env('BUSINESS_WITHDRAW_COMMISSION', 0.5),
            'weekly_discount' => [
                'maximum_free_withdraw' => env('MAXIMUM_FREE_WITHDRAW_TRANSACTIONS', 3),
                'maximum_free_amount' => env('MAXIMUM_FREE_WITHDRAW_AMOUNT', 1000)
            ],
        ]
    ],
    'csv_columns' => [
        'date' => 0,
        'user_id' => 1,
        'user_type' => 2,
        'operation_type' => 3,
        'amount' => 4,
        'currency' => 5,
    ],
    'user_types' => [
        'private',
        'business',
    ],
    'operation_types' => [
        'deposit',
        'withdraw',
    ],
    'date_format' => 'Y-m-d',
    'currency_exchange_base_url' => env('CURRENCY_EXCHANGE_BASE_URL'),
    'base_currency' => env('BASE_CURRENCY', 'EUR'),
    'non_decimal_currencies' => [
        'JPY' => 'JAPANESE YEN'
    ]
];
