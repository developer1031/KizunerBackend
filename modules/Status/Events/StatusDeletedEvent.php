<?php

namespace Modules\Status\Events;

use Modules\Kizuner\Models\Status;

class StatusDeletedEvent
{
    private $object;

    public function __construct(string $statusId)
    {
        $this->object = $statusId;
    }

    public function getObject()
    {
        return $this->object;
    }
}
