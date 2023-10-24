<?php

namespace Modules\Framework\Service\Loader;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Framework\Service\Facade\Environment;

class ViewLoader
{
    const CACHE_VIEW = 'views';

    public function getViewsCollection()
    {
        $cacheKey = md5(self::CACHE_VIEW);

        if (Cache::has($cacheKey) && Environment::isProduction()) {
            return Cache::get($cacheKey);
        }

        $modules = scandir(base_path('modules'));
        $moduleCollector = collect($modules);
        $viewCollector = collect([]);

        $moduleCollector->filter(function ($item) {
            return file_exists($this->viewPath($item));
        })->each(function($item) use ($viewCollector){
            $moduleName = $item;
            $views = simplexml_load_file($this->viewPath($item));

            $viewDef = empty(((array)$views)['view']) ? [] :
                (count(((array)$views)['view']) > 1 ? ((array)$views)['view'] : [$views->view]);
            $viewCollection = collect($viewDef);

            $viewCollection->each(function ($item) use ($viewCollector, $moduleName) {
                $viewCollector->push([
                    'alias'     => (string)$item->attributes()['alias'],
                    'path'      => base_path('modules/' . $moduleName .'/'. (string)$item->attributes()['path']),
                ]);
            });
        });
        Cache::put($cacheKey, $viewCollector, 3600);
        return $viewCollector;
    }

    /**
     * Get Module Service Path
     * @param string $moduleName
     * @return string
     */
    private function viewPath(string $moduleName): string
    {
        return base_path('modules/' . $moduleName . '/etc/views.xml');
    }
}
