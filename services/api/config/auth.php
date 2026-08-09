<?php

return [

    'defaults' => [
        'guard' => 'admin',
        'passwords' => 'admins',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'driver' => [
            'driver' => 'session',
            'provider' => 'drivers',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],

        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'drivers' => [
            'driver' => 'eloquent',
            'model' => App\Models\Driver::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password reset brokers
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'admins' => [
            'provider' => 'admins',
            'driver' => 'cache',
            'expire' => 60,
            'throttle' => 5,
        ],
    ],
];
