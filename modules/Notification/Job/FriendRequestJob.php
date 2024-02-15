<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Comment\Models\Comment;
use Modules\Kizuner\Models\User\Friend;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;

class FriendRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'friend-request';

    private $request;

    public function __construct(Friend $request)
    {
        $this->request = $request;
    }

    public function handle()
    {
        // $request = $this->request;

        // $requester = User::find($request->user_id);
        // $requesterMedia = $requester->medias()->where('type', 'user.avatar')->first();
        // $image = null;
        // if ($requesterMedia) {
        //     $image = \Storage::disk('gcs')->url($requesterMedia->thumb);
        // }

        // $message = $requester->name . ' send you a friend request';

        // $payload = [
        //     'relation' => [
        //         'id'        => $request->user_id,
        //         'type'      => 'user',
        //         'friend_request_id' => $request->id
        //     ],
        //     'type'          => self::TYPE,
        //     'created_at'    => $request->created_at,
        //     'message'       => '<b>' . $requester->name . '</b>' . ' send you a friend request'
        // ];

        // $token = UserDeviceToken::getUserDevice($request->friend_id, '');

        // if ($token) {
        //     $friend_request_pending = Friend::where('user_id', $request->friend_id)->where('status', 1)->count();
        //     $payload['image'] = $image;
        //     $payload['unread_count'] = intval(getUnreadNotification($request->friend_id)) + intval($friend_request_pending);
        //     PushNotificationJob::dispatch('sendBatchNotification', [
        //         [$token], [
        //             'topicName'     => 'kizuner',
        //             'title'         => 'Kizuner',
        //             'body'          => $message,
        //             'payload'       => $payload
        //         ],
        //     ]);
        // }

    }
}
