<?php

return [
  'refresh_token' => env('AUTOFLEET_REFRESH_TOKEN'),
  'end_point' => env('AUTOFLEET_END_POINT', 'https://api.autofleet.io/api'),
  'demand_source_id' => env('AUTOFLEET_DEMAND_SOURCE_ID'),
  'scrapper_url' => env('AUTOFLEET_SCRAPPER_URL'),
  'fleet_id' => env('AUTOFLEET_ENTITY_ID'),

  'business_models' => [
    'peterborough' => [
      'id' => '664e322d-5791-4e8e-b342-e34ed3e03c89',
      'no_opp_vendor' => '70420f5f-0d0f-4c1d-8397-a8745df913a6',
    ],
    'sudbury' => [
      'id' => 'ff752ca8-6665-4392-9293-71565aeeeaf7',
      'no_opp_vendor' => '',
    ],
    'medicine_hat' => [
      'id' => 'dd910ec5-5faa-4f3f-857e-767d4ea19ea6',
      'no_opp_vendor' => '717df7db-f4c5-43b3-80b2-529313488c32',
    ],
    'grande_praire' => [
      'id' => 'b27c5fea-70d8-4680-96ff-38059db20413',
      'no_opp_vendor' => 'fd43299d-f13b-4c7f-b8e2-5717fcfe52e4',
    ],
    'huntsville' => [
      'id' => 'bbde2071-72c0-4277-8a6d-d07d9211f9ab',
      'no_opp_vendor' => '1ad46a14-6c86-49a7-a704-020a79e8bd35',
    ],
    'lethbridge' => [
      'id' => '85c4950c-de32-46fc-8069-043ace3c9408',
      'no_opp_vendor' => '03da191a-b735-4149-8ecf-e8a520c24d1f',
    ],
    'testing' => [
      'id' => '112af8b9-09ce-4062-818f-5d4195dcd190',
      'no_opp_vendor' => '',
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
    'lethbridge' => [
      'business_model' => 'lethbridge',
      'timezone' => 'America/Edmonton',
    ]
  ]
];
