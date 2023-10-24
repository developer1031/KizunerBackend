<?php

namespace Modules\Guide\Domains;

use Modules\Category\Models\Category;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Guide\Domains\Entities\GuideEntity;

class Guide
{
    public $guide;

    public function __construct(GuideEntity $guide)
    {
        $this->guide = $guide;
    }

    public static function create(string $url, string $text, int $position, string $cover, string $duration)
    {
        $guide = EntityManager::create(GuideEntity::class);
        $guide->url         = $url;
        $guide->text        = $text;
        $guide->position    = $position;
        $guide->cover       = $cover;
        $guide->duration    = $duration;
        $guide->save();
        return $guide;
    }

    public static function update(string $id, string $url, string $text, int $position, string $cover)
    {
        $guideManager = EntityManager::getManager(GuideEntity::class);
        $guide        = $guideManager->find($id);
        $guide->url         = $url;
        $guide->text        = $text;
        $guide->position    = $position;
        $guide->cover       = $cover;
        $guide->save();
        return $guide;
    }

    public static function find(string $id)
    {
        $guideManager = EntityManager::getManager(GuideEntity::class);
        return $guideManager->find($id);
    }

    public static function delete(string $id)
    {
        $guideManager = EntityManager::getManager(GuideEntity::class);
        $guide        = $guideManager->find($id);
        return $guide->delete();
    }
}
