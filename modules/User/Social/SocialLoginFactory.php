<?php

namespace Modules\User\Social;

class SocialLoginFactory
{
    public function create($provider, $token, $secret = null)
    {
        $instance = null;
        if ($provider == 'facebook') {
            $instance =  app(FacebookLogin::class);
        }

        if ($provider == 'google') {
            $instance =  app(GoogleLogin::class);
        }

        if ($provider == 'apple') {
            $instance = app(AppleLogin::class);
        }

        if ($provider == 'twitter') {
          $instance = app(TwitterLogin::class);
        }

        return $instance->create($token, $secret);
    }
}
