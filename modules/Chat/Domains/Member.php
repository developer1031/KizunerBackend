<?php

namespace Modules\Chat\Domains;

use Carbon\Carbon;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Events\UpdateChat;
use Modules\Framework\Support\Facades\EntityManager;

class Member
{

    public $member;

    public function __construct(MemberEntity $member)
    {
        $this->member = $member;
    }

    /**
     * @param string $roomId
     * @param string $userId
     * @param bool $owner
     * @return MemberEntity
     */
    public static function create(string $roomId, string $userId, bool $owner = false)
    {
        if ($member = self::findByRoomIdAndUserId($roomId, $userId)) {
            return $member;
        }
        $member = EntityManager::create(MemberEntity::class);
        $member->room_id      = $roomId;
        $member->user_id      = $userId;
        $member->owner        = $owner;
        $member->seen_at      = Carbon::now();
        $member->save();
        event(new UpdateChat($userId));
        return $member;
    }

    public static function deleteByRoomIdAndMemberId(string $roomId, string $memberId)
    {
        if ($member = self::findByRoomIdAndUserId($roomId, $memberId)) {
            event(new UpdateChat($memberId));
            return $member->delete() === 1 ? true : false;
        }

        return false;
    }

    public static function findByRoomIdAndUserId(string $roomId, string  $memberId)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)
                            ->where('user_id', $memberId)
                            ->first();
    }

    public static function findByRoomId(string $roomId)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)->get();
    }

    public static function findFakeMemberByRoomId(string $roomId, $currentMemberId)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)->where('user_id', '<>', $currentMemberId)->first();
    }

    public static function deleteByRoomId(string $roomId)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)->delete();
    }

    public static function findByRoomIdExceptUserId(string $roomId, string $user_id)
    {
        $memberManager = EntityManager::getManager(MemberEntity::class);
        return $memberManager->where('room_id', $roomId)
            ->where('user_id', '<>', $user_id)
            ->first();
    }
}
