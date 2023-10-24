<?php

namespace Modules\Chat\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class UserStatusEntity extends UuidEntity
{
    protected $table = 'chat_user_statuses';
}
