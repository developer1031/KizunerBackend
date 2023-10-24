<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\EnvironmentManager;
use Modules\Framework\Service\Facade\Environment;

class EnvironmentServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->app->bind(
            'environment',
            EnvironmentManager::class
        );

        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Environment', Environment::class);
    }
}
