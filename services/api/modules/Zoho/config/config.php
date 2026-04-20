<?php

return [
    'name' => 'Zoho',
    'client_id'     => env('ZOHO_CLIENT_ID'),
    'client_secret' => env('ZOHO_CLIENT_SECRET'),
    'redirect_uri'  => env('ZOHO_REDIRECT_URI'),
    'api_domain'    => 'https://www.zohoapis.com/crm/v2',
    'zoho_admin_emails' => explode(',', env('ZOHO_ADMIN_EMAILS')),
];