<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Kizuner\Models\React;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Modules\User\Notifications\LikeEmail;
use Illuminate\Support\Facades\Notification as SysNotification;

class HangoutLikeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $react;

    public function __construct(React $react)
    {
        $this->react = $react;
    }

    public function handle()
    {
        $react = React::find($this->react->id);

        $reacter = User::find($react->user_id);
        $reacterMedia = $reacter->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($reacterMedia) {
            $image = \Storage::disk('gcs')->url($reacterMedia->thumb);
        }

        $action = ($react->react_type =='like') ? ' liked ' : ' shared ';
        $message = $reacter->name . $action .' your hangout';
        $type    = 'hangout-liked';

        $payload = [
            'relation' => [
                'id'        => $react->reactable_id,
                'type'      => 'hangout'
            ],
            'type'          => $type,
            'created_at'    => $react->created_at,
            'message'       => '<b>' . $reacter->name . '</b>' . ' liked on your hangout'
        ];

        $data = (new NotificationDto())
                    ->setUserId($react->reacted_user_id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType($type)
                    ->setUploadableId($reacterMedia ? $reacterMedia->uploadable_id : null);
        $notification = Notification::create($data);

        $token = UserDeviceToken::getUserDevice($react->reacted_user_id, "like_notification");
       

        if ($token) {
            $payload['image'] = $image;
            $payload['id'] = $notification->id;
            $payload['unread_count'] = getUnreadNotification($react->reacted_user_id);
            PushNotificationJob::dispatch('sendBatchNotification', [
                [$token], [
                    'topicName'     => 'kizuner',
                    'title'         => $notification->title,
                    'body'          => $notification->body,
                    'payload'       => $payload
                ],
            ]);
        }

        $emailReceiver = UserDeviceToken::getUserEmail($react->reacted_user_id, "like_email_notification");
        if($emailReceiver) {
            SysNotification::route('mail', $emailReceiver)
            ->notify(new LikeEmail('',$notification->title,$notification->body,$emailReceiver,""));
            }
    }
}
