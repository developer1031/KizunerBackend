<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Modules\Framework\Contracts\AbstractLoader;
use Modules\Framework\Contracts\LoaderInterface;
use Modules\Framework\Service\Facade\Environment;

class MigrationsLoader extends AbstractLoader implements LoaderInterface
{
    const CACHE_MIGRATION = 'migrations';

    /**
     * @inheritDoc
     */
    public function getCollection()
    {
        return $this->getMigrationCollection();
    }

    public function getMigrationCollection()
    {
        $cacheKey = md5(self::CACHE_MIGRATION);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $migrationCollector = collect([]);

        $this->getModuleCollection()
            ->filter(function($item) {
                $path = $this->getPath($item);
                return is_dir($path) && $path !== false;
            })
            ->each(function ($item) use ($migrationCollector) {
                $migrationCollector->push([
                    'path' => $this->getPath($item)
                ]);
            });

        Cache::put($cacheKey, $migrationCollector, 3600);
        return $migrationCollector;
    }

    /**
     * @param string $module
     * @return string
     */
    private function getPath(string $module)
    {
        return base_path('modules/' . $module . '/Migrations');
    }
}
