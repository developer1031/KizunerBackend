<?php

namespace Modules\Notification\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Message;
use Modules\Chat\Domains\Queries\MessageQuery;
use Modules\Chat\Http\Transformers\MessageTransformer;
use Modules\Kizuner\Models\User\Friend;
use Modules\Notification\Domains\NotificationEntity;

class BadgeController
{
    public function show(string $userId)
    {
        // get unread notification
        $countUnreadNotification = NotificationEntity::where([
            'user_id' => $userId,
            'status'  => 0
        ])->count();

        $countRoomHaveNewMessage = 0;

        $userRooms = MemberEntity::where('user_id', $userId)->get();

        foreach ($userRooms as $room) {
            $lastMessage = Message::getLastMessageByRoomId($room->room_id);

            if ($lastMessage && Carbon::create($lastMessage->create_at)->gt(Carbon::create($room->seen_at))) {
                $countRoomHaveNewMessage += 1;
            }
        }

        $countFriendRequest = Friend::where([
            'friend_id' => $userId,
            'status'    => 1
        ])->count();

        //if ($countRoomHaveNewMessage == 0 && $countUnreadNotification == 0 && $countFriendRequest == 0) {
        if ($countUnreadNotification == 0 && $countFriendRequest == 0) {
            return response()->json([
                'status' => false
            ], Response::HTTP_OK);
        }
        return response()->json([
            'status' => true
        ], Response::HTTP_OK);
    }
}
