<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Framework\Service\Loader\LiveWireLoader;

class ModuleLiveWireServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $components = (new LiveWireLoader())->getLiveWireCollection();

        $components->each(function($item) {
            Livewire::component($item['alias'], $item['class']);
        });
    }
}
