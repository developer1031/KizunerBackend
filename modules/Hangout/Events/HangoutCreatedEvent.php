<?php

namespace Modules\Hangout\Events;

use Modules\Kizuner\Models\Hangout;

class HangoutCreatedEvent
{
    private $object;

    public function __construct(Hangout $hangout)
    {
        $this->object = $hangout;
    }

    public function getObject()
    {
        return $this->object;
    }
}
