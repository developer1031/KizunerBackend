<?php

namespace Modules\Package\Domains\Repositories;

use Illuminate\Support\Collection;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Package\Domains\Entities\PackageEntity;
use Modules\Package\Domains\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{

    /**
     * @inheritDoc
     */
    public function get(): Collection
    {
        $packageManager = EntityManager::getManager(PackageEntity::class);
        return $packageManager->orderBy('point')->get();
    }
}
