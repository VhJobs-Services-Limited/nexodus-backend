<?php

declare(strict_types=1);

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

    'providers' => [
        'email' => [
            'default' => env('EMAIL_PROVIDER', 'ahasend'),
            'services' => [
                'ahasend' => App\Services\Mail\AhaSendService::class,
                'sendpulse' => App\Services\Mail\SendpulseService::class,
            ],
        ],
    ],

    'sendpulse' => [
        'base_url' => env('SENDPULSE_BASE_URL', ''),
        'secret' => env('SENDPULSE_SECRET', ''),
        'client_id' => env('SENDPULSE_CLIENT_ID', ''),
        'sender' => env('SENDPULSE_SENDER'),
        'sender_name' => env('SENDPULSE_SENDER_NAME', env('APP_NAME')),
        'templates' => array_map(function (string $item) {
            $template = explode(':', $item);

            return ['key' => $template[0] ?? null, 'value' => $template[1] ?? null];
        }, [...array_filter(explode(',', env('SENDPULSE_TEMPLATE', '')))]),
    ],

    'ahasend' => [
        'base_url' => env('AHASEND_BASE_URL', ''),
        'api_key' => env('AHASEND_API_KEY'),
        'sender' => env('AHASEND_SENDER', 'no-reply@vhjobs.xyz'),
        'sender_name' => env('AHASEND_SENDER_NAME', env('APP_NAME')),
    ],
];
