<?php

namespace Modules\Upload\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Upload\Models\Upload;

class UploadTransform extends TransformerAbstract
{
    public function transform(Upload $media)
    {
        $disk = \Storage::disk('gcs');
        $url = $disk->url($media->path);
        $thumbUrl = $disk->url($media->thumb);
        return [
            'id'    => $media->id,
            'path'  => $url,
            'thumb' => $thumbUrl,
            'type'  => $media->type
        ];
    }
}
