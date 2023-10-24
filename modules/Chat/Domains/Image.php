<?php

namespace Modules\Chat\Domains;

use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Entities\ImageEntity;
use Modules\Framework\Support\Facades\EntityManager;

class Image
{

    private $image;

    public function __construct(ImageEntity $image)
    {
        $this->image = $image;
    }

    public static function create(string $original, string $thumb, string $type='image')
    {
        $image = EntityManager::create(ImageEntity::class);
        $image->original = $original;
        $image->thumb    = $thumb;
        $image->type     = $type;
        $image->save();
        return $image;
    }

    public static function update(string $id, string $messageId)
    {
        $imageManager       = EntityManager::getManager(ImageEntity::class);
        $image              = $imageManager->find($id);
        $image->message_id  = $messageId;
        $image->save();
        return $image;
    }
}
