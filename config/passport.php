<?php

return [
    'oath' => [
        'url'           => env('OAUTH_PWD_GRANT_URL', env('APP_URL') . '/oauth/token'),
        'client_id'     => env('OAUTH_PWD_GRANT_CLIENT_ID', 2),
        'client_secret' => env('OAUTH_PWD_GRANT_CLIENT_SECRET')
    ]
];
