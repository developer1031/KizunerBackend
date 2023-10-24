<?php

namespace Modules\Chat\Domains\Actions;

use Modules\Chat\Domains\Image;

class BatchUpdateImageAction
{
    private $messageId;
    private $images;
    private $type;

    public function __construct($messageId, $images, $type='image')
    {
        $this->messageId = $messageId;
        $this->images    = collect($images);
        $this->type = $type;
    }

    public function execute()
    {
        return $this->updateImageData();
    }

    private function updateImageData()
    {
        $images = collect([]);

        $this->images->each(function ($item) use ($images){
            $images->push(Image::update($item, $this->messageId, $this->type));
        });
        return $images;
    }
}
