<?php

namespace Modules\Framework\Support\DB;

use Modules\Framework\Support\DB\Exceptions\EntityException;

class EntityService
{

    /**
     * Create new Entity Manager
     * @param $entityClass
     * @return mixed
     */
    public function create(string $entityClass)
    {
        return resolve($entityClass);
    }

    /**
     * Create new Entity Manager
     * @param $entityClass
     * @return mixed
     */
    public function getManager(string $entityClass)
    {
       return $this->create($entityClass);
    }

    /**
     * Get Repository of Entity
     * @param string $entityClass
     * @return mixed
     * @throws EntityException
     */
    public function getRepository(string $entityClass)
    {
        $entityObject = $this->create($entityClass);

        if (property_exists($entityObject, 'repository')) {
            if ($entityObject->repository) {
                return resolve($entityObject->repository);
            }
        }
        throw new EntityException('Property "repository" not exist or not defined, please define it');
    }
}
