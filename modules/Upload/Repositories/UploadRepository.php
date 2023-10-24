<?php

namespace Modules\Upload\Repositories;

use Modules\Upload\Contracts\UploadRepositoryInterface;
use Modules\Upload\Models\Upload;

class UploadRepository implements UploadRepositoryInterface
{

    /**
     * @param array $data
     * @return Upload
     */
    public function create(array $data)
    {
        $upload = new Upload($data);
        $upload->save();
        return $upload;
    }

    /**
     * @param $id
     * @return int
     */
    public function delete(string $id)
    {
        return Upload::destroy($id);
    }
}
