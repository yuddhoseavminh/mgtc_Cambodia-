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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    'telegram' => [
        'enabled' => env('TELEGRAM_ENABLED', false),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
        'message_thread_id' => env('TELEGRAM_MESSAGE_THREAD_ID'),
        'send_attachments' => env('TELEGRAM_SEND_ATTACHMENTS', true),
        'verify_ssl' => env('TELEGRAM_VERIFY_SSL', true),
        'timeout' => (int) env('TELEGRAM_TIMEOUT', 30),
        'connect_timeout' => (int) env('TELEGRAM_CONNECT_TIMEOUT', 10),
        'retry_times' => (int) env('TELEGRAM_RETRY_TIMES', 3),
        'retry_delay_ms' => (int) env('TELEGRAM_RETRY_DELAY_MS', 1200),
        'proxy' => env('TELEGRAM_PROXY'),
        'http_proxy' => env('TELEGRAM_HTTP_PROXY'),
        'https_proxy' => env('TELEGRAM_HTTPS_PROXY'),
    ],

];
