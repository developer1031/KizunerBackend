<?php

namespace Modules\Chat\Domains\Entities;

use Modules\Framework\Support\DB\UuidEntity;

class ImageEntity extends UuidEntity
{
    protected $table = 'chat_message_images';
}
