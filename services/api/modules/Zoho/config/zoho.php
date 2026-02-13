<?php

return [
    'client_id'        => env('ZOHO_CLIENT_ID'),
    'client_secret'    => env('ZOHO_CLIENT_SECRET'),
    'refresh_token'    => env('ZOHO_REFRESH_TOKEN'),
    'region'           => env('ZOHO_REGION', 'US'),
    'currentUserEmail' => env('ZOHO_CURRENT_USER_EMAIL'),
    'redirect_uri'     => env('ZOHO_REDIRECT_URI'),
    'token_path'       => storage_path('app/zoho/'),
];