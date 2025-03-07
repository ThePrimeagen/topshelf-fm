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

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    "twitch" => [
        "client_id" => env("TWITCH_CLIENT_ID"),
        "client_secret" => env("TWITCH_CLIENT_SECRET"),
        "redirect" => env("TWITCH_REDIRECT_URL"),
        "broadcaster_id" => env("TWITCH_CHANNEL_ID"),
        "friend_ids" => array_filter(explode(",", env("TWITCH_FRIEND_IDS", ''))),
    ],

    "turso" => [
        "url" => env("TURSO_DATABASE_URL"),
        "token" => env("TURSO_AUTH_TOKEN"),
    ]
];
