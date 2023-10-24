<?php

namespace Modules\Framework\Contracts;

abstract class AbstractLoader
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getModuleCollection()
    {
        $modules = scandir(base_path('modules'));
        return collect($modules);
    }
}
