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
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'openweather' => [
        'api_key' => env('OPENWEATHER_API_KEY', 'b21f6d5394907c41d84972c8fdb51999'),
    ],

    'google_analytics' => [
        'id' => env('GOOGLE_ANALYTICS_ID'),
    ],

    'mapbox' => [
        'access_token' => env('MAPBOX_ACCESS_TOKEN'),
        'api_key' => env('MAPBOX_ACCESS_TOKEN'), // Alias for consistency
    ],

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
        'map_id' => env('GOOGLE_MAPS_MAP_ID'),
    ],

    'onesignal' => [
        'app_id' => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
    ],

    'google_cloud' => [
        'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
        'key_file' => env('GOOGLE_CLOUD_KEY_FILE', 'storage/app/google-cloud-vision-key.json'),
        'api_key' => env('GOOGLE_CLOUD_API_KEY'), // Alternative to key_file
    ],

];
