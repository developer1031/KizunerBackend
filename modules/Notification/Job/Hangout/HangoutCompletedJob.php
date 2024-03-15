<?php

namespace Modules\Notification\Job\Hangout;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\HangoutHelpEmail;

class HangoutCompletedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $hangout;
    private $offer;

    public function __construct(Hangout $hangout, Offer $offer)
    {
        $this->hangout = $hangout;
        $this->offer = $offer;
    }

    public function handle()
    {
        $hangout = Hangout::find($this->hangout->id);
        $user_status = $this->offer->sender;

        $token = UserDeviceToken::getUserDevice($user_status->id, "hangout_help_notification");
        if ($token == null) {
            return;
        }

        $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($userMedia) {
            $image = \Storage::disk('gcs')->url($userMedia->thumb);
        }
        $message = $user_status->name . ' completed Hangout';
        $type    = 'offer_completed';

        $payload = [
            'relation' => [
                'id'        => $hangout->id,
                'type'      => 'hangout'
            ],
            'type'          => $type,
            'created_at'    => $hangout->created_at,
            'message'       => '<b>Hangout ' . $this->hangout->title . '</b>' . ' was completed.'
        ];

        $data = (new NotificationDto())
            ->setUserId($user_status->id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);

        $notification = Notification::create($data);

        $payload['image'] = $image;
        $payload['id'] = $notification->id;
        $payload['unread_count'] = getUnreadNotification($user_status->id);
        PushNotificationJob::dispatch('sendBatchNotification', [
            [$token], [
                'topicName'     => 'kizuner',
                'title'         => $notification->title,
                'body'          => $notification->body,
                'payload'       => $payload
            ],
        ]);

        // $emailReceiver = UserDeviceToken::getUserEmail($user_status->id, "hangout_help_notification");
        // if ($emailReceiver) {
        //     SysNotification::route('mail', $emailReceiver)
        //         ->notify(new HangoutHelpEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        // }
    }
}
