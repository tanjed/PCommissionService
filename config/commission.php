<?php

return [
    'rules' => [
        'deposit' => [
            'default' => 0.03,
        ],
        'withdraw' => [
            'default' => 0.3,
            'business' => 0.5,
            'weekly_discount' => [
                'maximum_free_withdraw' => 3,
                'maximum_free_amount' => 1000
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
    'currency_exchange_base_url' => 'https://developers.paysera.com/tasks/api/currency-exchange-rates',
    'base_currency' => 'EUR',
    'non_decimal_currencies' => [
        'JPY' => 'JAPANESE YEN'
    ]
];
