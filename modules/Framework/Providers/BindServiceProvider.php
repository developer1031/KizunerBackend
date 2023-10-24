<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\Loader\ServiceLoader;

class BindServiceProvider extends ServiceProvider
{

    public function register()
    {
       //
    }

    public function boot()
    {
        $this->regiterServiceCollection();
    }

    private function regiterServiceCollection()
    {
        $serviceCollection = (new ServiceLoader())->getServiceCollection();

        $serviceCollection['bind']->each(function ($item) {
            $this->app->bind(
                $item['alias'],
                $item['concreate']
            );
        });

        $serviceCollection['singleton']->each(function ($item) {
            $this->app->singleton(
                $item['alias'],
                $item['concreate']
            );
        });

        $serviceCollection['factory']->each(function ($item) {
            $this->app->bind(
                $item['alias'],
                $item['concreate']
            );

            $this->app->bind(
                $item['alias'] . 'Factory',
                $item['concreate'] . 'Factory'
            );
        });
    }
}
