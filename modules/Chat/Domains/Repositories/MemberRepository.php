<?php

namespace Modules\Chat\Domains\Repositories;

use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Framework\Support\Facades\EntityManager;

class MemberRepository implements MemberRepositoryInterface
{
    public function getByRoomId(string $roomId)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)->get();
    }

    public function getByUserId(string $userId, $perPage)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->distinct()
                            ->where('user_id', $userId)
                            ->orderBy('updated_at', 'desc')
                            ->paginate($perPage);
    }
}
