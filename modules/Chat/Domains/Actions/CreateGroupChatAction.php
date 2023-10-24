<?php

namespace Modules\Chat\Domains\Actions;

use App\User;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Room;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Job\Chat\ChatRoomAddUserJob;
use Modules\Notification\Notification\PushNotificationJob;

class CreateGroupChatAction
{

    private $userId;

    private $memberIds;

    public function __construct(string $userId, array $memberIds)
    {
        $this->userId       = $userId;
        $this->memberIds    = $memberIds;
    }

    public function execute()
    {
        return $this->addMembers($this->createGroupRoom());
    }

    private function createGroupRoom()
    {
        return Room::create(RoomEntity::TYPE_GROUP);
    }

    private function addMembers(RoomEntity $room)
    {
        Member::create($room->id, $this->userId, true);

        $user = User::find($this->userId);
        $message = $user->name . ' add you to a group chat';
        $token = [];
        foreach ($this->memberIds as $memberId) {
            $member = Member::create($room->id, $memberId);
            $payload = [
                'relation' => [
                    'id'        => $member->room_id,
                    'type'      => 'room',
                ],
                'type'          => 'chat-members',
                'message'       => $message
            ];

            $token[] = UserDeviceToken::getUserDevice($member->user_id, '');
        }

        if (!empty($token)) {
            $payload['unread_count'] = getUnreadNotification($member->user_id);
            PushNotificationJob::dispatch('sendBatchNotification', [
                $token, [
                    'topicName'     => 'kizuner',
                    'title'         => 'Kizuner',
                    'body'          => $message,
                    'payload'       => $payload
                ],
            ]);
        }

        return $room;
    }
}
