<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Service\Facade\Environment;

class LiveWireLoader extends AbstractLoader
{
    const CACHE_LIVE_WIRE = 'live_wire';

    public function getLiveWireCollection()
    {
        $cacheKey = md5(self::CACHE_LIVE_WIRE);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $liveWireCollector = collect([]);

        ($this->getModuleCollection())->filter(function ($item) {
            return file_exists($this->liveWirePath($item));
        })->each(function($item) use ($liveWireCollector){
            $liveWires = simplexml_load_file($this->liveWirePath($item));

            $liveWiresDef = empty(((array)$liveWires)['component']) ? [] :
                (count(((array)$liveWires)['component']) > 1 ? ((array)$liveWires)['component'] : [$liveWires->component]);
            $viewCollection = collect($liveWiresDef);

            $viewCollection->each(function ($item) use ($liveWireCollector) {
                $liveWireCollector->push([
                    'alias'     => (string)$item->attributes()['alias'],
                    'class'     => (string)$item->attributes()['class'],
                ]);
            });
        });

        Cache::put($cacheKey, $liveWireCollector, 3600);
        return $liveWireCollector;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function liveWirePath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/livewires.xml');
    }
}
