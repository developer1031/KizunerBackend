<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mockery\Matcher\Not;
use Modules\Helps\Models\HelpOffer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Domains\User;
use Modules\User\Notifications\HangoutHelpEmail;

class NewHelpOfferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $offer;

    public function __construct(HelpOffer $offer)
    {
        $this->offer = $offer;
    }

    public function handle()
    {
        $offer = $this->offer;

        $token = UserDeviceToken::getUserDevice($offer->receiver_id, "hangout_help_notification");


        $sender = User::find($offer->sender_id);
        $senderMedia = $sender->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($senderMedia) {
            $image = \Storage::disk('gcs')->url($senderMedia->thumb);
        }

        $message = $sender->name . ' would like to help you';
        $type    = 'new-offer';

        $payload = [
            'relation' => [
                'id'        => $offer->help_id,
                'type'      => 'help'
            ],
            'type'          => $type,
            'created_at'    => $offer->created_at,
            'message'       => '<b>' . $sender->name . '</b>' . ' would like to help you'
        ];

        $data = (new NotificationDto())
            ->setUserId($offer->receiver_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($senderMedia ? $senderMedia->uploadable_id : null);
        $notification = Notification::create($data);

        if ($token == null) {
            return;
        }

        $payload['image'] = $image;
        $payload['id'] = $notification->id;
        $payload['unread_count'] = getUnreadNotification($offer->receiver_id);
        PushNotificationJob::dispatch('sendBatchNotification', [
            [$token], [
                'topicName'     => 'kizuner',
                'title'         => $notification->title,
                'body'          => $notification->body,
                'payload'       => $payload
            ],
        ]);

        // $emailReceiver = UserDeviceToken::getUserEmail($offer->receiver_id, "hangout_help_notification");
        // if ($emailReceiver) {
        //     SysNotification::route('mail', $emailReceiver)
        //         ->notify(new HangoutHelpEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        // }

    }
}
