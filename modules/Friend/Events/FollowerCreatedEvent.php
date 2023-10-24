<?php

namespace Modules\Friend\Events;

use Modules\Kizuner\Models\User\Follow;

class FollowerCreatedEvent
{
    private $object;

    public function __construct(Follow $follow)
    {
        $this->object = $follow;
    }

    public function getObject()
    {
        return $this->object;
    }
}
