<?php

namespace Modules\Upload\Contracts;

interface UploadRepositoryInterface
{
    public function create(array $data);

    public function delete(string $id);
}
