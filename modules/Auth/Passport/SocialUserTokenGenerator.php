<?php

namespace Modules\Auth\Passport;

use GuzzleHttp\Client;

class SocialUserTokenGenerator
{
    public static function handle(string $providerName, string $accessToken)
    {
        $http   =    app(Client::class);
        return $http->post(config('passport.oath.url'), [
                'form_params' => [
                    'grant_type'        => 'social',
                    'client_id'         => config('passport.oath.client_id') ,
                    'client_secret'     => config('passport.oath.client_secret'),
                    'provider'          => $providerName,
                    'access_token'      => $accessToken,
                    'scope'             => ''
                ],
            ]);
    }
}
