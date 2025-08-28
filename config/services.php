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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'affiliate' => [
        'dublin_office' => [
            'latitude' => (float) env('DUBLIN_OFFICE_LATITUDE', 53.3340285),
            'longitude' => (float) env('DUBLIN_OFFICE_LONGITUDE', -6.2535495),
        ],
        'distance_limit_km' => (float) env('AFFILIATE_DISTANCE_LIMIT_KM', 100),
        'data_file' => env('AFFILIATE_DATA_FILE', 'affiliates.txt'),
        'cache_ttl' => (int) env('AFFILIATE_CACHE_TTL', 3600),
    ],

];
