<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Kizuner\Models\User\Friend;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;

class FriendAcceptedJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'friend-accepted';

    private $request;

    public function __construct(Friend $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        // $request = $this->request;

        // $user = User::find($request->friend_id);
        // $userMedia = $user->medias()->where('type', 'user.avatar')->first();
        // $image = null;
        // if ($userMedia) {
        //     $image = \Storage::disk('gcs')->url($userMedia->thumb);
        // }

        // $message = $user->name . ' accepted your friend request';

        // $payload = [
        //     'relation' => [
        //         'id'        => $request->friend_id,
        //         'type'      => 'user',
        //         'friend_request_id' => $request->id
        //     ],
        //     'type'          => self::TYPE,
        //     'created_at'    => $request->created_at,
        //     'message'       => '<b>' . $user->name . '</b>' . ' accepted your friend request'
        // ];

        // $data = (new NotificationDto())
        //     ->setUserId($request->user_id)
        //     ->setTitle('Kizuner')
        //     ->setBody($message)
        //     ->setPayload($payload)
        //     ->setType(self::TYPE)
        //     ->setUploadableId($userMedia ? $userMedia->id : null);
        // $notification = Notification::create($data);

        // $token = UserDeviceToken::getUserDevice($request->user_id, '');

        // if ($token) {
        //     $payload['image'] = $image;
        //     $payload['id'] = $notification->id;
        //     $payload['unread_count'] = getUnreadNotification($request->user_id);
        //     PushNotificationJob::dispatch('sendBatchNotification', [
        //         [$token], [
        //             'topicName'     => 'kizuner',
        //             'title'         => $notification->title,
        //             'body'          => $notification->body,
        //             'payload'       => $payload
        //         ],
        //     ]);
        // }
    }
}
