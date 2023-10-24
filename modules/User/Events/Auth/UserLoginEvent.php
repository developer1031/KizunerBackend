<?php

namespace Modules\User\Events\Auth;

use App\User;

class UserLoginEvent
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getObject()
    {
        return $this->user;
    }
}
