<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }


    public function boot()
    {

        $frameworkProviders = collect([
            EnvironmentServiceProvider::class,
            ModuleMiddlewareProvider::class,
            ModuleRouteProvider::class,
            ModuleViewServiceProvider::class,
            ModuleLiveWireServiceProvider::class,
            BindServiceProvider::class,
            ModuleAutoServiceProvider::class,
            ModuleEventProvider::class,
            ModuleMigrationServiceProvider::class
        ]);

        $frameworkProviders->each(function($item) {
            $this->app->register($item);
        });
    }
}
