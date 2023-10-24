<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Service\Facade\Environment;

class ModuleServiceLoader extends AbstractLoader
{
    const CACHE_SERVICE_PROVIDERS = 'service_providers';

    public function getServiceProvidersCollection()
    {
        $cacheKey = md5(self::CACHE_SERVICE_PROVIDERS);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $providerCollector = collect([]);

        $this->getModuleCollection()
            ->filter(function ($item) {
                $path = $this->getPath($item);
                return $item !== 'Framework' && is_dir($path) && $path !== false;
            })
            ->each(function($module) use ($providerCollector) {
                $path = $this->getPath($module);
                $files = scandir($path);
                $filesCollection = collect($files);

                $filesCollection
                    ->filter(function ($item) use ($module) {
                        return is_file($this->getPath($module) . '/' . $item);
                    })
                    ->each(function ($item) use ($providerCollector, $module) {
                    $namespace = 'Modules\\' . $module . '\\Providers\\';
                    $class = str_replace('.php', '', $item);
                    $providerCollector->push($namespace . $class);
                });
            });
        Cache::put($cacheKey, $providerCollector, 3600);
        return $providerCollector;
    }

    /**
     * @param string $module
     * @return string
     */
    private function getPath(string $module)
    {
        return base_path('modules/' . $module . '/Providers');
    }
}
