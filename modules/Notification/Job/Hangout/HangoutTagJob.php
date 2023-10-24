<?php

namespace Modules\Notification\Job\Hangout;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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

class HangoutTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $hangout;

    public function __construct(Hangout $hangout)
    {
        $this->hangout = $hangout;
    }

    public function handle()
    {
        $hangout = Hangout::find($this->hangout->id);
        $user_status = User::find($hangout->user_id);
        $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($userMedia) {
            $image = \Storage::disk('gcs')->url($userMedia->thumb);
        }
        $message = $user_status->name . ' tagged you to Hangout';
        $type    = 'tagged_help';

        $payload = [
            'relation' => [
                'id'        => $hangout->id,
                'type'      => 'hangout'
            ],
            'type'          => $type,
            'created_at'    => $hangout->created_at,
            'message'       => '<b>' . $user_status->name . '</b>' . ' tagged you to Hangout',
            'unread_count'  => 0
        ];

        /*
        $data = (new NotificationDto())
            ->setUserId($hangout->user_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
        $notification = Notification::create($data);
        */

        if($hangout->room_id) {
            $members = MemberEntity::where('room_id', $hangout->room_id)->get();
            foreach ($members as $member) {
                if($member->user_id != auth()->user()->id) {
                    $token = UserDeviceToken::getUserDevice($member->user_id, '');
                    if ($token) {
                        $data = (new NotificationDto())
                            ->setUserId($member->user_id)
                            ->setTitle('Kizuner')
                            ->setBody($message)
                            ->setPayload($payload)
                            ->setType($type)
                            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
                        $notification = Notification::create($data);

                        $payload['image'] = $image;
                        $payload['id'] = $notification->id;
                        $payload['unread_count'] = getUnreadNotification($member->user_id);
                        PushNotificationJob::dispatch('sendBatchNotification', [
                            [$token], [
                                'topicName'     => 'kizuner',
                                'title'         => $notification->title,
                                'body'          => $notification->body,
                                'payload'       => $payload
                            ],
                        ]);
                    }
                }
            }
        }

        if($hangout->friends) {
            $friends = $hangout->friends;
            foreach ($friends as $friend) {
                $token = UserDeviceToken::getUserDevice($friend);
                if ($token) {

                    $data = (new NotificationDto())
                        ->setUserId($friend)
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
                }

                //Noti via email
                $user_friend = \App\User::where('id', $friend)->where('email_notification', 1)->first();
                if($user_friend) {
                    $user_friend->notify(new MailTag('hangout'));
                }
            }
        }
    }
}
