<?php

namespace Modules\Notification\Job;

use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\Mails\MailTag;
use Modules\Notification\Notification\PushNotificationJob;

class StatusTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $status;

    public function __construct(Status $status)
    {
        $this->status = $status;
    }

    public function handle()
    {
        $status = Status::find($this->status->id);
        $user_status = User::find($status->user_id);
        $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($userMedia) {
            $image = \Storage::disk('gcs')->url($userMedia->thumb);
        }
        $message = $user_status->name . ' tagged you to status';
        $type    = 'tagged_status';

        $payload = [
            'relation' => [
                'id'        => $status->id,
                'type'      => 'status'
            ],
            'type'          => $type,
            'created_at'    => $status->created_at,
            'message'       => '<b>' . $user_status->name . '</b>' . ' tagged you to status'
        ];

        /*
        $data = (new NotificationDto())
            ->setUserId($status->user_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
        $notification = Notification::create($data);
        */

        if($status->friends) {
            $friends = json_decode($status->friends);
            foreach ($friends as $friend) {
                $token = UserDeviceToken::getUserDevice($friend,'');
                if ($token) {
                    $data = (new NotificationDto())
                        ->setUserId($friend)
                        ->setTitle('Kizuner')
                        ->setBody($message)
                        ->setPayload($payload)
                        ->setType($type)
                        ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);

                    //Save DB
                    $notification = Notification::create($data);

                    //Pust notification to app
                    $payload['image'] = $image;
                    $payload['id'] = $notification->id;
                    $payload['unread_count'] = getUnreadNotification($friend);
                    PushNotificationJob::dispatch('sendBatchNotification', [
                        [$token], [
                            'topicName'     => 'kizuner',
                            'title'         => $notification->title,
                            'body'          => $notification->body,
                            'payload'       => $payload
                        ],
                    ]);
                }

                //Noti via email
                $user_friend = User::where('id', $friend)->where('email_notification', 1)->first();
                if($user_friend) {
                    $user_friend->notify(new MailTag('status'));
                }
            }
        }
    }
}
