<?php

namespace Modules\Notification\Job\Help;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Helps\Models\Help;
use Modules\Kizuner\Models\React;
use Modules\Kizuner\Models\Status;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\Mails\MailTag;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;

class HelpTagJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $help;

    public function __construct(Help $help)
    {
        $this->help = $help;
    }

    public function handle()
    {
        $help = Help::find($this->help->id);
        $user_status = User::find($help->user_id);
        $userMedia = $user_status->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($userMedia) {
            $image = \Storage::disk('gcs')->url($userMedia->thumb);
        }
        $message = $user_status->name . ' tagged you to Help';
        $type    = 'tagged_help';

        $payload = [
            'relation' => [
                'id'        => $help->id,
                'type'      => 'help'
            ],
            'type'          => $type,
            'created_at'    => $help->created_at,
            'message'       => '<b>' . $user_status->name . '</b>' . ' tagged you to Help'
        ];

        /*
        $data = (new NotificationDto())
            ->setUserId($help->user_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType($type)
            ->setUploadableId($userMedia ? $userMedia->uploadable_id : null);
        $notification = Notification::create($data);
        */

        if($help->room_id) {
            $members = MemberEntity::where('room_id', $help->room_id)->get();
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
                }
            }
        }

        if($help->friends) {
            $friends = $help->friends;
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
                    $user_friend->notify(new MailTag('help'));
                }
            }
        }
    }
}
