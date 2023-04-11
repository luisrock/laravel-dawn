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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'test_mode' => env('STRIPE_TEST', false), 
        'enabled' => env('STRIPE_PAYMENTS_ENABLED', false),
        'key' => (env('STRIPE_TEST') === false) ? env('STRIPE_KEY') : env('STRIPE_KEY_TEST'),
        'secret' => (env('STRIPE_TEST') === false) ? env('STRIPE_SECRET') : env('STRIPE_SECRET_TEST'),
        'webhook_secret' => (env('STRIPE_TEST') === false) ? env('STRIPE_WEBHOOK_SECRET') : env('STRIPE_WEBHOOK_SECRET_TEST'),
        'price_id' => (env('STRIPE_TEST') === false) ? env('STRIPE_PRICE_ID') : env('STRIPE_PRICE_ID_TEST'),
        'dash_link' => (env('STRIPE_TEST') === false) ? env('STRIPE_DASH_LINK') : env('STRIPE_DASH_LINK_TEST'),
    ],
    'google' => [
        'enabled' => env('GOOGLE_AUTH_ENABLED', false),
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],
    
];
