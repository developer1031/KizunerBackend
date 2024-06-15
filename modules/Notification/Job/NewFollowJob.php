<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Comment\Models\Comment;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\FollowEmail;

class NewFollowJob implements ShouldQueue
{

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'new-follow';

    private $follow;

    public function __construct(Follow $follow)
    {
        $this->follow = $follow;
    }

    public function handle()
    {
        $follow = $this->follow;

        $token = UserDeviceToken::getUserDevice($follow->follow_id, "follow_notification");

        if ($token == null) {
            return;
        }

        $emailReceiver = UserDeviceToken::getUserEmail($follow->follow_id, "follow_notification");
        if ($emailReceiver == null) {
            return;
        }

        $follower = User::find($follow->user_id);
        $followerMedia = $follower->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($followerMedia) {
            $image = \Storage::disk('gcs')->url($followerMedia->thumb);
        }

        $message = $follower->name . ' followed you';

        $payload = [
            'relation' => [
                'id'        => $follow->user_id,
                'type'      => 'user',
            ],
            'type'          => self::TYPE,
            'created_at'    => $follow->created_at,
            'message'       => '<b>' . $follower->name . '</b>' . ' followed you'
        ];

        $data = (new NotificationDto())
            ->setUserId($follow->follow_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType(self::TYPE)
            ->setUploadableId($followerMedia ? $followerMedia->uploadable_id : null);
        $notification = Notification::create($data);

        $payload['image'] = $image;
        $payload['id'] = $notification->id;
        $payload['unread_count'] = getUnreadNotification($follow->follow_id);
        PushNotificationJob::dispatch('sendBatchNotification', [
            [$token], [
                'topicName'     => 'kizuner',
                'title'         => $notification->title,
                'body'          => $notification->body,
                'payload'       => $payload
            ],
        ]);

        // if ($emailReceiver) {
        //     SysNotification::route('mail', $emailReceiver)
        //         ->notify(new FollowEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        // }
    }
}
