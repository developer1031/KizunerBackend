<?php

namespace Modules\Chat\Image;

use Intervention\Image\Facades\Image;

class GCPImageUploadAction
{

    private $images;

    public function __construct($images)
    {
        $this->images = collect($images);
    }

    public function execute()
    {
        $uploadedImages = collect([]);

        $this->images->each(function($item) use ($uploadedImages) {

            $uploadedImages->push($this->upload($item));
        });
        return $uploadedImages;
    }

    private function upload($image)
    {
        $disk = \Storage::disk('gcs');
        $original = Image::make($image)->encode('jpg', 90);
        $fileName = pathinfo($image->hashName(), PATHINFO_FILENAME);
        $saveOriginal   =  'chats/' . date('Y/m/d') . '/' . $fileName . '.jpg';
        $saveThumb      =  'chats/' . date('Y/m/d') . '/' .  $fileName . '_thumb.jpg';

        $originalRs = $original->stream();
        $disk->put(
            $saveOriginal,
            $originalRs
        );

        $original->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $thumbRs = $original->stream()->detach();
        $disk->put(
            $saveThumb,
            $thumbRs
        );
        return [
            'original' => $saveOriginal,
            'thumb'    => $saveThumb
        ];
    }
}
