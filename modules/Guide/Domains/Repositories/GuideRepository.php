<?php

namespace Modules\Guide\Domains\Repositories;

use Modules\Framework\Support\Facades\EntityManager;
use Modules\Guide\Domains\Entities\GuideEntity;
use Modules\Guide\Domains\Repositories\Contracts\GuideRepositoryInterface;

class GuideRepository implements GuideRepositoryInterface
{
    public function get($perPage)
    {
        $guideManager = EntityManager::getManager(GuideEntity::class);
        return $guideManager->where('status', 1)->orderBy('position')->paginate($perPage);
    }
}
