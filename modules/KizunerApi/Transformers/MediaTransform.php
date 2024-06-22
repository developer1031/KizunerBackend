<?php

namespace Modules\KizunerApi\Transformers;

use League\Fractal\TransformerAbstract;
use Modules\Upload\Models\Upload;

class MediaTransform extends TransformerAbstract
{
    public function transform(Upload $upload)
    {
        try {
            $disk = \Storage::disk('gcs');
            $url = $disk->url($upload->path);
            $thumbUrl = ($upload->thumb != '') ? $disk->url($upload->thumb) : $url;
            return [
                'id'        => $upload->id,
                'path'      => $url,
                //'path'      => 'https://file-examples-com.github.io/uploads/2017/04/file_example_MP4_1280_10MG.mp4',
                //'path'      => 'https://storage.googleapis.com/inapps_gcs_devs/Skype_Video.mp4',
                'thumb'     => $thumbUrl,
                'type'      => $upload->type
            ];
        } catch (\Exception $e) {
            return [
                'id'        => 0,
                'path'      => $upload->path,
                'thumb'     => $upload->thumb,
                'type'      => $upload->type
            ];
        }
    }
}
