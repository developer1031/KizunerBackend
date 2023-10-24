<?php

namespace Modules\Chat\Domains;

use Modules\Chat\Domains\Entities\UserStatusEntity;
use Modules\Framework\Support\Facades\EntityManager;

class UserStatus
{
    public $userStatus;

    public function __construct(UserStatusEntity $userStatus)
    {
        $this->userStatus = $userStatus;
    }

    public function create($userId, $status = false)
    {
        $userStatus = EntityManager::create(UserStatusEntity::class);
        $userStatus->user_id = $userId;
        $userStatus->status  = $status;
        $userStatus->save();
        return $userStatus;
    }

    public static function findByUserId($userId)
    {
        $userStManager = EntityManager::getManager(UserStatusEntity::class);
        return $userStManager->where('user_id', $userId)->first();
    }

    public static function updateStatus($userId, $status)
    {
        $userStatus    = self::findByUserId($userId);

        if (!$userStatus) {
            return self::create($userId, $status);
        }

        $userStatus->status = $status;
        $userStatus->save();
        return $userStatus;
    }
}
