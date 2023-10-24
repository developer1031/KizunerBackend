<?php

namespace Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use League\OAuth2\Server\AuthorizationServer;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Modules\Auth\Grants\SocialGrant;
use Modules\Auth\Contracts\SocialUserResolverInterface;

class SocialGrantServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->resolving(AuthorizationServer::class, function (AuthorizationServer $server) {
            $server->enableGrantType(
                $this->makeSocialGrant(),
                Passport::tokensExpireIn()
            );
        });
    }

    /**
     * @return SocialGrant
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function makeSocialGrant(): SocialGrant
    {
        $grant = new SocialGrant(
            $this->app->make(SocialUserResolverInterface::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
