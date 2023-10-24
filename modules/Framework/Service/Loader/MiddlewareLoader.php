<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Contracts\LoaderInterface;
use Modules\Framework\Service\Facade\Environment;

class MiddlewareLoader extends AbstractLoader implements LoaderInterface
{

    const CACHE_MIDDLEWARE = 'middlewares';

    public function getMdCollection()
    {
        $cacheKey = md5(self::CACHE_MIDDLEWARE);
        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $mdCollector = collect([]);

        $this->getModuleCollection()
            ->filter(function ($item) {
                return file_exists($this->middlewarePath($item));
            })->each(function ($item) use ($mdCollector) {
                $mds = simplexml_load_file($this->middlewarePath($item));

                $mdDef = empty(((array)$mds)['middleware']) ? [] :
                    (count(((array)$mds)['middleware']) > 1 ? ((array)$mds)['middleware'] : [$mds->middleware]);
                $mdCollection = collect($mdDef);

                $mdCollection->each(function ($item) use ($mdCollector) {
                    $mdCollector->push([
                        'alias'     => (string)$item->attributes()['name'],
                        'class'   => (string)$item->attributes()['class'],
                    ]);
                });
            });

        Cache::put($cacheKey, $mdCollector, 3600);
        return $mdCollector;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function middlewarePath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/middlewares.xml');
    }

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return $this->getMdCollection();
    }
}
