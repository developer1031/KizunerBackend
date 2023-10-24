<?php

namespace Modules\Notification\Job\Chat;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Queries\MessageQuery;
use Modules\Chat\Http\Transformers\MessageTransformer;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\KizunerApi\Transformers\UserTransform;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Illuminate\Support\Facades\Notification as SysNotification;

class NewMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'chat-message';
    const TYPE_CHAT_BOT = 'chat-bot';

    private $message;
    private $relateUser;

    public function __construct(MessageEntity $message, $relateUser=null)
    {
        $this->message = $message;
        $this->relateUser = $relateUser;
    }

    public function handle()
    {
        $messageObject = EntityManager::getManager(MessageEntity::class)->find($this->message->id);
        $noti_type = self::TYPE;

        $msg_user = User::find($messageObject->user_id);
        $chat_member = Member::findFakeMemberByRoomId($messageObject->room_id, $messageObject->user_id);
        $chat_member = User::find($chat_member->user_id);

        if( ($msg_user && $msg_user->is_fake) || ($chat_member && $chat_member->is_fake)) {
            $noti_type = self::TYPE_CHAT_BOT;
        }

        //Get All Room Members
        $members = DB::table('chat_members')
                    ->where('room_id', $messageObject->room_id)
                    ->where('user_id', '<>', $messageObject->user_id)
                    ->groupBy('chat_members.user_id')
                    ->get();

        $chatRoom = DB::table('chat_rooms')
                        ->where('id', $messageObject->room_id)
                        ->first();

        $relatedUser = $this->relateUser ? fractal(\App\User::find($this->relateUser->skillable_id), new UserTransform()) : null;

        $message = 'You got a new message';
        $payload = [
            'relation' => [
                'id'        => $chatRoom->id,
                'type'      => 'room',
                'relatedUser' => $relatedUser
            ],
            //'type' => self::TYPE
            'type' => $noti_type,
            'message' => fractal((new MessageQuery($messageObject->id))->execute(), new MessageTransformer())
        ];

        if ($chatRoom->type === RoomEntity::TYPE_GROUP) {
            if ($chatRoom->name !== null) {
                $message .= ' from ' . $chatRoom->name;
            }
        } else if ($chatRoom->type === RoomEntity::TYPE_PERSONAL) {
            $sender = User::find($messageObject->user_id);
            $message .= ' from ' . $sender->name;
        }
        $token = collect();
        $members->each(function($item) use ($message, $payload, $token) {
            $userToken = UserDeviceToken::getUserDevice($item->user_id, "message_notification");
            if ($userToken) {
                $token->push($userToken);
            }
        });
        if (!empty($token)) {
            $payload['unread_count'] = 0;
            PushNotificationJob::dispatch('sendBatchNotification', [
                $token->toArray(), [
                    'topicName'     => 'kizuner',
                    'title'         => 'Kizuner',
                    'body'          => $message,
                    'payload'       => $payload
                ],
            ]);
        }
    }
}
