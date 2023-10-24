<?php

namespace Modules\Framework\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\Framework\Service\Loader\RouteLoader;

class ModuleRouteProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $routeCollection = (new RouteLoader())->getCollection();
        $routeCollection->each(function($item) {
            Route::middleware($item['middleware'])
                ->prefix($item['prefix'])
                ->namespace($item['namespace'])
                ->group($item['path']);
        });
    }
}
