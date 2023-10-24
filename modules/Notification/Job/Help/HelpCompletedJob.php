<?php

namespace Modules\Notification\Job\Help;

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
use Modules\Helps\Models\HelpOffer;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\HangoutHelpEmail;

class HelpCompletedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $help;
    private $offer;

    public function __construct(Help $help, HelpOffer $offer)
    {
        $this->help = $help;
        $this->offer = $offer;
    }

    public function handle()
    {
        $help = Help::find($this->help->id);
        $user_status = $this->offer->receiver;
        $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($userMedia) {
            $image = \Storage::disk('gcs')->url($userMedia->thumb);
        }
        $message = $user_status->name . ' completed Help';
        $type    = 'offer_completed';

        $payload = [
            'relation' => [
                'id'        => $help->id,
                'type'      => 'help'
            ],
            'type'          => $type,
            'created_at'    => $help->created_at,
            'message'       => '<b>Help ' . $this->help->title . '</b>' . ' was completed.'
        ];

        $data = (new NotificationDto())
            ->setUserId($user_status->id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);

        $notification = Notification::create($data);

        $token = UserDeviceToken::getUserDevice($user_status->id, "hangout_help_notification");

        if ($token) {
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
        }

        $emailReceiver = UserDeviceToken::getUserEmail($user_status->id, "hangout_help_notification");
        if ($emailReceiver) {
            SysNotification::route('mail', $emailReceiver)
                ->notify(new HangoutHelpEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        }
    }
}
