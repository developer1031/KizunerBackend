<?php

namespace Modules\Notification\Job\Hangout;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\Mails\MailTag;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;

class HangoutFitUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $hangout;

    public function __construct(Hangout $hangout, User $user_id)
    {
        $this->hangout = $hangout;
        $this->user = \App\User::find($user_id->id);
    }

    public function handle()
    {
        Log::info($this->user);

        if($this->user) {
            $hangout = Hangout::find($this->hangout->id);
            $user_status = $this->user;
            $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
            $image = null;
            if ($userMedia) {
                $image = \Storage::disk('gcs')->url($userMedia->thumb);
            }

            $message = $user_status->name . ' has got Hangout fit your skills';
            $type    = 'tagged_help';

            $payload = [
                'relation' => [
                    'id'        => $hangout->id,
                    'type'      => 'hangout'
                ],
                'type'          => $type,
                'created_at'    => $hangout->created_at,
                'message'       => '<b>' . $user_status->name . '</b>' . ' has got Hangout fit your skills'
            ];

            $token = null;
            if($this->user->fcm_token && $this->user->notification) {
                $token = $this->user->fcm_token;
            }

            if ($token) {
                $data = (new NotificationDto())
                    ->setUserId($this->user->id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType($type)
                    ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
                $notification = Notification::create($data);

                $payload['image'] = $image;
                $payload['id'] = $notification->id;
                $payload['unread_count'] = 0;
                PushNotificationJob::dispatch('sendBatchNotification', [
                    [$token], [
                        'topicName'     => 'kizuner',
                        'title'         => $notification->title,
                        'body'          => $notification->body,
                        'payload'       => $payload
                    ],
                ]);

                $this->hangout->is_sent_to_users = true;
                $this->hangout->save();
            }
        }
    }
}
