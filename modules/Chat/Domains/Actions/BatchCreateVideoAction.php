<?php

namespace Modules\Chat\Domains\Actions;

use Modules\Chat\Domains\Image;

class BatchCreateVideoAction
{

    private $images;

    public function __construct($images)
    {
        $this->images = $images;
    }

    public function execute()
    {
        return $this->saveUploadedImages();
    }

    private function saveUploadedImages()
    {
        $images = collect([]);

        $this->images->each(function ($item) use ($images){
            $image = Image::create($item['original'], $item['thumb']);
            $image->original = \Storage::disk('gcs')->url($image->original);
            $image->thumb    = \Storage::disk('gcs')->url($image->thumb);
            $images->push($image);
        });

        return $images;
    }
}
