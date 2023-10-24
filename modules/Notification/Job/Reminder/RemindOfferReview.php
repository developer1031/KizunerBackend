<?php

namespace Modules\Notification\Job\Reminder;

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
use Modules\Helps\Models\HelpOffer;

class RemindOfferReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'review-reminder';

    private $offer;

    /**
     * @param Offer|HelpOffer $offer
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function handle()
    {
        $offer = $this->offer;

        $receiver = User::find($offer->receiver_id);

        $message =  'You can review ' . $receiver->name . ' now';

        $payload = [
            'relation' => [
                'id'        => $offer->id,
                'type'      => 'offer'
            ],
            'type'          => self::TYPE,
            'created_at'    => $offer->created_at,
            'message'       => 'You can review <b>' . $receiver->name . '</b> now'
        ];

        $data = (new NotificationDto())
                    ->setUserId($offer->sender_id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType(self::TYPE);
        $notification = Notification::create($data);

        //Update Status Remind
        $offer->review_remind = true;
        $offer->save();

        $token = UserDeviceToken::getUserDevice($offer->sender_id, "hangout_help_notification");

        if ($token) {
            $payload['id'] = $notification->id;
            $payload['image'] = null;
            $payload['unread_count'] = getUnreadNotification($offer->sender_id);
            PushNotificationJob::dispatch('sendBatchNotification', [
                [$token], [
                    'topicName'     => 'kizuner',
                    'title'         => $notification->title,
                    'body'          => $notification->body,
                    'payload'       => $payload
                ],
            ]);
        }

//        $message =  'You can review your guests now';
//
//        $payload = [
//            'relation' => [
//                'id'        => $offer->id,
//                'type'      => 'offer'
//            ],
//            'type'          => self::TYPE,
//            'created_at'    => $offer->created_at,
//            'message'       => $message
//        ];
//
//        $data = (new NotificationDto())
//            ->setUserId($offer->receiver_id)
//            ->setTitle('Kizuner')
//            ->setBody($message)
//            ->setPayload($payload)
//            ->setType(self::TYPE);
//        $notification = Notification::create($data);
//
//        $receiverToken = UserDeviceToken::getUserDevice($offer->receiver_id);
//        if ($receiverToken) {
//            $payload['id'] = $notification->id;
//            $payload['image'] = null;
//            PushNotificationJob::dispatch('sendBatchNotification', [
//                [$token], [
//                    'topicName'     => 'kizuner',
//                    'title'         => $notification->title,
//                    'body'          => $notification->body,
//                    'payload'       => $payload
//                ],
//            ]);
//        }
    }
}
