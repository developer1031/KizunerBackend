<?php

namespace Modules\Friend\Events;

use Modules\Kizuner\Models\User\Friend;

class FriendCreatedEvent
{
    private $object;

    public function __construct(Friend $friend)
    {
        $this->object = $friend;
    }

    public function getObject()
    {
        return $this->object;
    }
}
