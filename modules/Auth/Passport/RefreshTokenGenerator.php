<?php

namespace Modules\Auth\Passport;

use GuzzleHttp\Client;

class RefreshTokenGenerator
{
    public static function handle(string $refreshToken)
    {
        $http   =    app(Client::class);
        return  $http->post(config('passport.oath.url'), [
            'form_params' => [
                'grant_type'        => 'refresh_token',
                'client_id'         => config('passport.oath.client_id') ,
                'client_secret'     => config('passport.oath.client_secret'),
                'refresh_token'     => $refreshToken,
                'scope'             => ''
            ],
        ]);
    }
}
