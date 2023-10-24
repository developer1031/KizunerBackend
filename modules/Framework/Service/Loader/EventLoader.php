<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Contracts\LoaderInterface;
use Modules\Framework\Service\Facade\Environment;

class EventLoader extends AbstractLoader implements LoaderInterface
{
    const CACHE_EVENT = 'events';

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return $this->getEventCollection();
    }

    public function getEventCollection()
    {
        $cacheKey = md5(self::CACHE_EVENT);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $subscriberCollector = collect([]);
        $listenerCollector   = collect([]);
        $discoverCollector   = collect([]);

        $this->getModuleCollection()->filter(function ($item) {
            return file_exists($this->eventPath($item));
        })->each(function ($item) use ($subscriberCollector, $listenerCollector, $discoverCollector) {
            $moduleName = $item;
            $events = simplexml_load_file($this->eventPath($item));

            $listenerDef = empty(((array)$events)['listener']) ? [] :
                (count(((array)$events)['listener']) > 1 ? ((array)$events)['listener'] : [$events->listener]);

            $subscriberDef = empty(((array)$events)['subscriber']) ? [] :
                (count(((array)$events)['subscriber']) > 1 ? ((array)$events)['subscriber'] : [$events->subscriber]);

            $discoverDef = empty(((array)$events)['discover']) ? [] :
                (count(((array)$events)['discover']) > 1 ? ((array)$events)['discover'] : [$events->discover]);

            $listenerCollection     = collect($listenerDef);
            $subscriberCollection   = collect($subscriberDef);
            $discoverCollection     = collect($discoverDef);

            $listenerCollection->each(function ($item) use ($listenerCollector) {
                $listenerCollector->push([
                    'name' => (string)$item->attributes()['name'],
                    'handler' => (string)$item->attributes()['handler'],

                ]);
            });

            $subscriberCollection->each(function ($item) use ($subscriberCollector) {
                $subscriberCollector->push([
                    'name' => (string)$item->attributes()['name'],

                ]);
            });

            $discoverCollection->each(function ($item) use ($discoverCollector, $moduleName) {
                $discoverCollector->push([
                    'path' => base_path('modules/' . $moduleName . '/' . (string)$item->attributes()['path']),
                ]);
            });
        });

        $eventCollector = [
            'listeners'   => $listenerCollector,
            'subscribers' => $subscriberCollector,
            'discovers'    => $discoverCollector
        ];

        Cache::put($cacheKey, $eventCollector, 3600);
        return $eventCollector;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function eventPath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/events.xml');
    }
}
