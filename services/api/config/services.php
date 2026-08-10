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
        ],
        'desk' => [
            'base_url' => env('ZOHO_DESK_BASE_URL', 'https://desk.zoho.com/api/v1'),
            'department_id' => env('ZOHO_DESK_DEPARTMENT_ID'),
        ],
    ],


    'ghl' => [
        'api_key' => env('GHL_API_KEY'),
        'location_id' => env('GHL_LOCATION_ID'),
        'pipeline_id' => env('GHL_PIPELINE_ID'),
        'pipeline_stage_id' => env('GHL_PIPELINE_STAGE_ID'),
        'api_version' => env('GHL_API_VERSION', '2021-07-28'),
        'base_url' => env('GHL_BASE_URL', 'https://services.leadconnectorhq.com'),
    ],

    'google_sheets' => [
        'access_token' => env('GOOGLE_SHEETS_ACCESS_TOKEN'),
        'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID'),
        'base_url' => env('GOOGLE_SHEETS_BASE_URL', 'https://sheets.googleapis.com/v4/spreadsheets'),
        'ranges' => [
            'signups' => env('GOOGLE_SHEETS_RANGE_SIGNUPS', 'Signups!A:H'),
            'rides' => env('GOOGLE_SHEETS_RANGE_RIDES', 'Rides!A:F'),
        ],
    ],

    'airtable' => [
        'api_key' => env('AIRTABLE_API_KEY'),
        'base_id' => env('AIRTABLE_BASE_ID'),
        'tables' => [
            'riders' => env('AIRTABLE_TABLE_RIDERS', 'Riders'),
            'drivers' => env('AIRTABLE_TABLE_DRIVERS', 'Drivers'),
        ],
    ],

    'twilio' => [
        'enabled' => (bool) env('TWILIO_ENABLED', false),
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'sms_service_sid' => env('TWILIO_SMS_SERVICE_SID'),
        'from' => env('TWILIO_FROM'),
        'user_notification_number' => env('TWILIO_USER_NOTIFICATION_NUMBER'),
        'driver_notification_number' => env('TWILIO_DRIVER_NOTIFICATION_NUMBER'),
        'debug_to' => env('TWILIO_DEBUG_TO'),
    ],

];

