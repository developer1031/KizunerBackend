<?php

namespace Modules\Framework\Service\Facade;

use Illuminate\Support\Facades\Facade;


/**
 * @method static \Modules\Framework\Service\EnvironmentManager isProduction()
 * @method static \Modules\Framework\Service\EnvironmentManager isDev()
 *
 * @see \Modules\Framework\Service\EnvironmentManager
 */
class Environment extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'environment';
    }
}
