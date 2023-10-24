<?php

namespace Modules\Notification\Job\Reminder;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Kizuner\Models\Offer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\NotificationService;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Modules\Helps\Models\HelpOffer;
use Illuminate\Support\Facades\Notification as SysNotification;

class RemindHangout implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'offer-reminder';

    private $offer;

    /**
     * @param Offer|HelpOffer $offer
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    public function handle(NotificationService $notificationService)
    {
        $offer = $this->offer;
        $gap = Carbon::now()->diffInMinutes($offer->start);
        $sender = User::find($offer->sender_id);
        $receiver = User::find($offer->receiver_id);

        $senderMessage = 'You have a hangout with ' . $receiver->name . ' in '.$gap.' minutes';
        $receiverMessage = 'You have a hangout with ' . $sender->name . ' in '.$gap.' minutes';
        $senderPayload = [
            'relation' => [
                'id'        => $offer->hangout_id,
                'type'      => 'hangout'
            ],
            'type'          => self::TYPE,
            'created_at'    => $offer->created_at,
            'message'       => 'You have a hangout with <b>' . $receiver->name . '</b> in '.$gap.' minutes'
        ];

        $receiverPayload = [
            'relation' => [
                'id'        => $offer->hangout_id,
                'type'      => 'hangout'
            ],
            'type'          => self::TYPE,
            'created_at'    => $offer->created_at,
            'message'       => 'You have a hangout with <b>' . $sender->name . '</b> in '.$gap.' minutes'
        ];

        $senderData = (new NotificationDto())
                        ->setUserId($offer->sender_id)
                        ->setTitle('Kizuner')
                        ->setBody($senderMessage)
                        ->setPayload($senderPayload)
                        ->setType(self::TYPE);
        $senderNoti = Notification::create($senderData);

        $receiverData = (new NotificationDto())
                        ->setUserId($offer->receiver_id)
                        ->setTitle('Kizuner')
                        ->setBody($receiverMessage)
                        ->setPayload($receiverPayload)
                        ->setType(self::TYPE);
        $receiverNoti = Notification::create($receiverData);

        //Update Status Remind
        $offer->offer_remind = true;
        $offer->save();

        $token = UserDeviceToken::getUserDevice($offer->sender_id, "hangout_help_notification");

        if ($token) {
            $payload['image'] = null;
            $payload['id'] = $senderNoti->id;
            call_user_func_array(
                [
                    $notificationService,
                    'sendBatchNotification'
                ], [
                    [$token], [
                        'topicName'     => 'kizuner',
                        'title'         => $senderNoti->title,
                        'body'          => $senderNoti->body,
                        'payload'       => $senderPayload
                    ],
                ]);
        }

        $token = UserDeviceToken::getUserDevice($offer->receiver_id, "hangout_help_notification");

        if ($token) {
            $payload['image'] = null;
            $payload['id'] = $receiverNoti->id;
            call_user_func_array(
                [
                    $notificationService,
                    'sendBatchNotification'
                ], [
                [$token], [
                    'topicName'     => 'kizuner',
                    'title'         => $receiverNoti->title,
                    'body'          => $receiverNoti->body,
                    'payload'       => $receiverPayload
                ],
            ]);
        }
    }
}
