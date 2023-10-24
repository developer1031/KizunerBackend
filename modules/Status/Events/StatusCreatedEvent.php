<?php

namespace Modules\Status\Events;

use Modules\Kizuner\Models\Status;

class StatusCreatedEvent
{
    private $object;

    public function __construct(Status $status)
    {
        $this->object = $status;
    }

    public function getObject()
    {
        return $this->object;
    }
}
