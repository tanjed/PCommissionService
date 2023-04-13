<?php

return [
    'commission_rules' => [
        'deposit' => [
            'default' => 0.3,
        ],
        'withdraw' => [
            'default' => 0.3,
            'business' => 0.5,
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
];
