<?php

namespace Modules\Chat\Domains\Actions;

use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Queries\RoomMemberQuery;
use Modules\Chat\Domains\Room;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Job\Chat\ChatRoomAddUserJob;
use Modules\Notification\Notification\PushNotificationJob;

class UpdateGroupMemberAction
{

    private $roomId;

    private $members;

    public function __construct(string $roomId, $members)
    {
        $this->roomId   = $roomId;
        $this->members  = $members;
    }

    public function execute()
    {
        $message = auth()->user()->name . ' add you to a group chat';
        $token = [];
        foreach ($this->members as $member) {
            $memberObj = Member::create($this->roomId, $member);

            $payload = [
                'relation' => [
                    'id'        => $memberObj->room_id,
                    'type'      => 'room',
                ],
                'type'          => 'chat-members',
                'message'       => $message
            ];

            $token[] = UserDeviceToken::getUserDevice($memberObj->user_id, '');
        }

        if (!empty($token)) {
            $payload['unread_count'] = getUnreadNotification($memberObj->user_id);
            PushNotificationJob::dispatch('sendBatchNotification', [
                $token, [
                    'topicName'     => 'kizuner',
                    'title'         => 'Kizuner',
                    'body'          => $message,
                    'payload'       => $payload
                ],
            ]);
        }

        return (new RoomMemberQuery(Room::find($this->roomId)))->execute();
    }
}
