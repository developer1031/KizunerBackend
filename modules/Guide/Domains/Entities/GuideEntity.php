<?php

namespace Modules\Guide\Domains\Entities;

use Modules\Category\Models\Category;
use Modules\Framework\Support\DB\UuidEntity;
use Modules\Guide\Domains\Repositories\Contracts\GuideRepositoryInterface;

class GuideEntity extends UuidEntity
{
    protected $table = 'guide_guides';

    public $repository = GuideRepositoryInterface::class;

    public function categories()
    {
        return $this->morphToMany(Category::class, 'categoryable');
    }
}
