<?php

namespace Modules\Kizuner\Repositories;

use Modules\Upload\Models\Upload;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;

class MediaRepository implements MediaRepositoryInterface
{

    public function update(string $id, array $data)
    {
        $media = Upload::find($id);
        $media->update($data);
        return $media;
    }
}
