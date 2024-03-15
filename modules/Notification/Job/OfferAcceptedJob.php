<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Kizuner\Models\Offer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Modules\User\Notifications\HangoutHelpEmail;
use Illuminate\Support\Facades\Notification as SysNotification;

class OfferAcceptedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function handle()
    {
        $offer = $this->offer;

        $token = UserDeviceToken::getUserDevice($offer->sender_id, "hangout_help_notification");
        if ($token == null) {
            return;
        }

        //Get receiver information
        $reveicer = User::find($offer->receiver_id);
        $receiverMedia = $reveicer->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($receiverMedia) {
            $image = \Storage::disk('gcs')->url($receiverMedia->thumb);
        }

        $message = $reveicer->name . ' accepted your hangout request';
        $type    = 'offer-accepted';

        $payload = [
            'relation' => [
                'id'        => $offer->hangout_id,
                'type'      => 'hangout'
            ],
            'type'          => $type,
            'created_at'    => $offer->updated_at,
            'message'       => '<b>' . $reveicer->name . '</b>' . ' accepted your hangout request'
        ];

        $data = (new NotificationDto())
                    ->setUserId($offer->sender_id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType($type)
                    ->setUploadableId($receiverMedia ? $receiverMedia->uploadable_id : null);
        $notification = Notification::create($data);

        $payload['image'] = $image;
        $payload['id'] = $notification->id;
        $payload['unread_count'] = getUnreadNotification($offer->sender_id);
        PushNotificationJob::dispatch('sendBatchNotification', [
            [$token], [
                'topicName'     => 'kizuner',
                'title'         => $notification->title,
                'body'          => $notification->body,
                'payload'       => $payload
            ],
        ]);

        // $emailReceiver = UserDeviceToken::getUserEmail($offer->sender_id, "hangout_help_notification");
        // if ($emailReceiver) {
        //     SysNotification::route('mail', $emailReceiver)
        //         ->notify(new HangoutHelpEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        // }

    }
}
