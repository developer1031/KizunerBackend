<?php

namespace Modules\Upload\Event;

use Illuminate\Queue\SerializesModels;
use Modules\Upload\Models\Upload;

class UploadEvent
{
    use SerializesModels;

    private $object;

    public function __construct(Upload $upload)
    {
        $this->object = $upload;
    }

    public function getObject()
    {
        return $this->object;
    }
}
