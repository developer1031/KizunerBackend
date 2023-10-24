<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\Loader\MigrationsLoader;

class ModuleMigrationServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $mgCollection = (new MigrationsLoader())->getCollection()->pluck('path')->toArray();
        $this->loadMigrationsFrom($mgCollection);
    }
}
