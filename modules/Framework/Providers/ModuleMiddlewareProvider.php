<?php

namespace Modules\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Framework\Service\Loader\MiddlewareLoader;
use Illuminate\Routing\Router;

class ModuleMiddlewareProvider extends ServiceProvider
{

    /**
     * @param Router $router
     */
    public function boot(Router $router)
    {
        $mdCollection = (new MiddlewareLoader())->getCollection();
        $mdCollection->each(function($item) use ($router) {
            $router->aliasMiddleware($item['alias'], $item['class']);
        });
    }
}
