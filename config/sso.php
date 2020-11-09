<?php

return [
    // 主域名
    'domain' => env('DOMAIN_URL'),
    // OAUTH2
    'oauth'  => [
        'client_id'     => env('OAUTH_CLIENT_ID'),
        'client_secret' => env('OAUTH_CLIENT_SECRET'),
        'redirect_uri'  => env('OAUTH_CALLBACK'),

        // 支持列表：authorization_code,
        'grant_type'    => 'authorization_code'
    ],
];
