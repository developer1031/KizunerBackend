<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Contracts\LoaderInterface;
use Modules\Framework\Service\Facade\Environment;

class ServiceLoader extends AbstractLoader implements LoaderInterface
{

    const CACHE_SERVICE = 'service';

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return $this->getServiceCollection();
    }

    /**
     * @return array|mixed
     */
    public function getServiceCollection()
    {
        $cacheKey = md5(self::CACHE_SERVICE);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $bindCollector = collect([]);
        $factoryCollector = collect([]);
        $singletonCollector = collect([]);

        ($this->getModuleCollection())->filter(function ($item, $value) {
            return file_exists($this->servicePath($item));
        })->each(function ($item, $key) use ($bindCollector, $singletonCollector, $factoryCollector) {

            $services = simplexml_load_file($this->servicePath($item));

            $bindDef =
                empty(((array)$services)['bind']) ? [] :
                    (count(((array)$services)['bind']) > 1 ? ((array)$services)['bind'] : [$services->bind]);
            $singletonDef =
                empty(((array)$services)['singleton']) ? [] :
                    (count(((array)$services)['singleton']) > 1 ? ((array)$services)['singleton'] : [$services->singleton]);
            $factoryDef =
                empty(((array)$services)['factory']) ? [] :
                    (count(((array)$services)['factory']) > 1 ? ((array)$services)['factory'] : [$services->factory]);

            $bindCollection = collect($bindDef);
            $factoryCollection = collect($factoryDef);
            $singletonCollection = collect($singletonDef);


            $bindCollection->filter(function($item) {
                return !empty((string)$item->attributes()['alias']);
            })->each(function ($item) use ($bindCollector) {
                $bindCollector->push([
                    'alias' => (string)$item->attributes()['alias'],
                    'concreate' => (string)$item->attributes()['concreate']
                ]);
            });

            $factoryCollection->filter(function($item) {
                    return !empty((string)$item->attributes()['alias']);
                })->each(function ($item) use ($factoryCollector) {
                    $factoryCollector->push([
                        'alias' => (string)$item->attributes()['alias'],
                        'concreate' => (string)$item->attributes()['concreate']
                    ]);
            });

            $singletonCollection->filter(function($item) {
                    return !empty((string)$item->attributes()['alias']);
                })->each(function ($item) use ($singletonCollector) {
                    $singletonCollector->push([
                        'alias' => (string)$item->attributes()['alias'],
                        'concreate' => (string)$item->attributes()['concreate']
                    ]);
            });
        });
        $services = [
            'bind'          => $bindCollector,
            'singleton'     => $singletonCollector,
            'factory'       => $factoryCollector
        ];

        Cache::put($cacheKey, $services, 3600);
        return $services;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function servicePath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/services.xml');
    }
}
