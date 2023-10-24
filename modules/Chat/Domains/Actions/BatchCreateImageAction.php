<?php

namespace Modules\Chat\Domains\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Image;

class BatchCreateImageAction
{

    private $images;
    private $type;

    public function __construct($images, $type='image')
    {
        $this->images = $images;
        $this->type = $type;
    }

    public function execute()
    {
        return $this->saveUploadedImages();
    }

    private function saveUploadedImages()
    {
        $images = collect([]);
        $type = $this->type;

        $this->images->each(function ($item) use ($images, $type) {

            $image = Image::create($item['original'], $item['thumb'], $type);
            $image->original = \Storage::disk('gcs')->url($image->original);
            $image->thumb    = \Storage::disk('gcs')->url($image->thumb);
            $images->push($image);
        });

        return $images;
    }
}
