<?php

namespace Modules\Hangout\Events;

class HangoutDeletedEvent
{
    private $object;

    public function __construct(string $hangoutId)
    {
        $this->object = $hangoutId;
    }

    public function getObject()
    {
        return $this->object;
    }
}
