<?php

namespace Modules\Auth\Passport;

use GuzzleHttp\Client;

class UserTokenGenerator
{
    public static function handle(string $email, string $password)
    {
        $http   =    app(Client::class);
        return  $http->post(config('passport.oath.url'), [
                'form_params' => [
                    'grant_type'        => 'password',
                    'client_id'         => config('passport.oath.client_id') ,
                    'client_secret'     => config('passport.oath.client_secret'),
                    'username'          => $email,
                    'password'          => $password,
                    'scope'             => ''
                ],
        ]);
    }
}
