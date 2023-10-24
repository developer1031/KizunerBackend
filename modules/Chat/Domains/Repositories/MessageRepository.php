<?php

namespace Modules\Chat\Domains\Repositories;

use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Chat\Domains\Repositories\Contracts\MessageRepositoryInterface;
use Modules\Framework\Support\Facades\EntityManager;

class MessageRepository implements MessageRepositoryInterface
{

    public function getByRoomId(string $roomId, $perPage)
    {
        $messageManager = EntityManager::getManager(MessageEntity::class);
        return $messageManager->where('room_id', $roomId)
                              ->orderBy('created_at')
                              ->paginate($perPage);
    }
}
