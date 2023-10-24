<?php

namespace Modules\Chat\Domains\Events;

use Modules\Framework\Support\Events\Traits\DataEvent;

class RoomDeletedEvent
{
    use DataEvent;

    public function __construct(string $roomId)
    {
        $this->data = $roomId;
    }
}
