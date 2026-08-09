<?php
return [
    'enabled' => env('RECAPTCHAV3_ENABLED', true),
    'origin' => env('RECAPTCHAV3_ORIGIN', 'https://www.google.com/recaptcha'),
    'secret' => env('RECAPTCHAV3_SECRET', ''),
    'locale' => env('RECAPTCHAV3_LOCALE', 'en')
];
