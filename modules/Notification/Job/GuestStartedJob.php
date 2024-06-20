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

class GuestStartedJob implements ShouldQueue
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

        $token = UserDeviceToken::getUserDevice($offer->receiver_id, "hangout_help_notification");


        $message = 'need to start hangout';
        $type = 'hangout_required_start';

        $payload = [
            'relation' => [
                'id' => $offer->help_id,
                'type' => 'hangout'
            ],
            'type' => $type,
            'created_at' => $offer->created_at,
            'message' => 'need to start hangout'
        ];

        $data = (new NotificationDto())
            ->setUserId($offer->receiver_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type);

        $notification = Notification::create($data);

        if ($token) {
            $payload['image'] = null;
            $payload['id'] = $notification->id;
            $payload['unread_count'] = getUnreadNotification($offer->receiver_id);
            PushNotificationJob::dispatch('sendBatchNotification', [
                [$token],
                [
                    'topicName' => 'kizuner',
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'payload' => $payload
                ],
            ]);
        }
    }
}
