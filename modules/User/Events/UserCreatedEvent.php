<?php

namespace Modules\User\Events;

use App\User;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use SerializesModels;

    private $object;

    public function __construct(User $user)
    {
        $this->object = $user;
    }

    public function getObject()
    {
        return $this->object;
    }
}
