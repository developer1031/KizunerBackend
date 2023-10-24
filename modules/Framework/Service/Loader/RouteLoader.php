<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Contracts\LoaderInterface;
use Modules\Framework\Service\Facade\Environment;

class RouteLoader extends AbstractLoader implements LoaderInterface
{

    const CACHE_ROUTE = 'routes';

    public function getRouteCollection()
    {
        $cacheKey = md5(self::CACHE_ROUTE);
        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $routeCollector = collect([]);

        $this->getModuleCollection()
            ->filter(function ($item) {
                return file_exists($this->routePath($item));
            })->each(function ($item) use ($routeCollector) {
                $moduleName = $item;
                $routes = simplexml_load_file($this->routePath($item));

                $routesDef = empty(((array)$routes)['route']) ? [] :
                    (count(((array)$routes)['route']) > 1 ? ((array)$routes)['route'] : [$routes->route]);
                $routeCollection = collect($routesDef);

                $routeCollection->each(function ($item) use ($routeCollector, $moduleName) {
                    $middlewares = collect(explode(',', (string)$item->attributes()['middleware']));
                    $middleware = $middlewares->map(function ($item) {
                        return trim($item);
                    });

                    $routeCollector->push([
                        'namespace' => (string)$item->attributes()['namespace'],
                        'path' => base_path('modules/' . $moduleName . '/' . (string)$item->attributes()['path']),
                        'prefix' => (string)$item->attributes()['prefix'],
                        'middleware' => $middleware->toArray()
                    ]);
                });
            });

        Cache::put($cacheKey, $routeCollector, 3600);
        return $routeCollector;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function routePath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/routes.xml');
    }

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return $this->getRouteCollection();
    }
}
