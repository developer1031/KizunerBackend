<?php

namespace Modules\Chat\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class MemberEntity extends UuidEntity
{
    protected $table = 'chat_members';
}
