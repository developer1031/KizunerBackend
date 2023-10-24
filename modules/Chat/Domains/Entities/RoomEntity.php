<?php

namespace Modules\Chat\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class RoomEntity extends UuidEntity
{

    const TYPE_PERSONAL     = 'personal';
    const TYPE_GROUP        = 'group';
    const TYPE_PUBLIC_GROUP = 'public_group';
    const TYPE_LOCATION     = 'location';

    const STATUS_ACTIVE     = 'active';
    const STATUS_INACTIVE   = 'inactive';

    protected $table = 'chat_rooms';
}
