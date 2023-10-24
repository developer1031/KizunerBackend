<?php

namespace Modules\Chat\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class MessageEntity extends UuidEntity
{
    protected $table = 'chat_messages';
}
