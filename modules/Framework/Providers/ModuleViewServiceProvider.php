<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\Loader\ViewLoader;

class ModuleViewServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $viewCollection = (new ViewLoader())->getViewsCollection();

        $viewCollection->each(function($item) {
            $this->app['view']->addNamespace(
                $item['alias'],
                $item['path']
            );
        });
    }
}
