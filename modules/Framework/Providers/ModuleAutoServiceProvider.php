<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\Loader\ModuleServiceLoader;

class ModuleAutoServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $providersCollection = (new ModuleServiceLoader())->getServiceProvidersCollection();
        $providersCollection->each(function($item) {
            $this->app->register($item);
        });
    }
}
