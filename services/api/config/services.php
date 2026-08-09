<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'chatwoot' => [
        'api_key' => env('CHATWOOT_API_KEY'),
        'base_url' => env('CHATWOOT_BASE_URL', 'https://coms.ydriveapp.com'),

        'clients' => [
            'account_id' => env('CHATWOOT_CLIENT_ACCOUNT_ID', ''),
        ]
    ],

    'zoho' => [
        'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
        'client_id' => env('ZOHO_CLIENT_ID'),
        'client_secret' => env('ZOHO_CLIENT_SECRET'),
        'crm' => [
            'base_url' => env('ZOHO_BASE_URL', 'https://www.zohoapis.com/crm/v8'),
        ]

    ]
];
