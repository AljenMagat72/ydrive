<?php

return [
    'refresh_token' => env('AUTOFLEET_REFRESH_TOKEN'),
    'end_point' => env('AUTOFLEET_END_POINT', 'https://api.autofleet.io/api'),
    'demand_source_id' => env('AUTOFLEET_DEMAND_SOURCE_ID'),
    'scrapper_url' => env('AUTOFLEET_SCRAPPER_URL'),
    'fleet_id' => env('AUTOFLEET_ENTITY_ID'),
    'minimum_acceptance_rate' => 60,
    'minimum_scheduled_hours' => 60,

    'business_models' => [
        'peterborough' => [
            'id' => '664e322d-5791-4e8e-b342-e34ed3e03c89',
        ],
        'sudbury' => [
            'id' => 'ff752ca8-6665-4392-9293-71565aeeeaf7',
        ],
        'medicine_hat' => [
            'id' => 'dd910ec5-5faa-4f3f-857e-767d4ea19ea6',
        ],
        'grande_praire' => [
            'id' => 'b27c5fea-70d8-4680-96ff-38059db20413',
        ],
        'huntsville' => [
            'id' => 'bbde2071-72c0-4277-8a6d-d07d9211f9ab',
        ],
        'lethbridge' => [
            'id' => '85c4950c-de32-46fc-8069-043ace3c9408',
        ],
        'testing' => [
            'id' => '112af8b9-09ce-4062-818f-5d4195dcd190',
        ],
    ],
    'cities' => [
        'peterborough' => [
            'business_model' => 'peterborough',
            'timezone' => 'America/Toronto',
        ],
        'sudbury' => [
            'business_model' => 'sudbury',
            'timezone' => 'America/Toronto',
        ],
        'grande_praire' => [
            'business_model' => 'grande_praire',
            'timezone' => 'America/Edmonton',
        ],
        'medicine_hat' => [
            'business_model' => 'medicine_hat',
            'timezone' => 'America/Edmonton',
        ],
        'cobourg' => [
            'business_model' => 'peterborough',
            'timezone' => 'America/Toronto',
        ],
        'lindsay' => [
            'business_model' => 'peterborough',
            'timezone' => 'America/Toronto',
        ],
        'huntsville' => [
            'business_model' => 'huntsville',
            'timezone' => 'America/Toronto',
        ],
        'lethbridge' => [
            'business_model' => 'lethbridge',
            'timezone' => 'America/Edmonton',
        ]
    ]
];
