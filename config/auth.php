<?php

return [
    'defaults' => [
        'guard'     => 'api',
        'passwords' => 'utilisateurs',
    ],

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'utilisateurs',
        ],
        'api' => [
            'driver'   => 'sanctum',
            'provider' => 'utilisateurs',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],
        'utilisateurs' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Utilisateur::class,
        ],
    ],

    'passwords' => [
        'utilisateurs' => [
            'provider' => 'utilisateurs',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),
];
