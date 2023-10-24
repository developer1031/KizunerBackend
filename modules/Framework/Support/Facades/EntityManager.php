<?php

namespace Modules\Framework\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Framework\Support\DB\EntityService;

/**
 * @method static  create(string $entityClass)
 * @method static  getManager(string $entityClass)
 * @method static  getRepository(string $entityClass)
 *
 * @see EntityService
 */
class EntityManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'EntityService';
    }
}
