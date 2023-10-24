<?php

namespace Modules\User\Social;

class SocialLoginFactory
{
    public function create($provider, $token)
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

        return $instance->create($token);
    }
}
