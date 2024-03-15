<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Comment\Models\Comment;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Domains\User;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\CommentEmail;

class StatusCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle()
    {
        $comment = $this->comment;

        $token = UserDeviceToken::getUserDevice($comment->commented_user_id, "comment_notification");
        if ($token == null) {
            return;
        }

        $commenter = User::find($comment->user_id);
        $commenterMedia = $commenter->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($commenterMedia) {
            $image = \Storage::disk('gcs')->url($commenterMedia->thumb);
        }

        $message = $commenter->name . ' commented on your status';
        $type    = 'status-comment';

        $payload = [
            'relation' => [
                'id'        => $comment->commentable_id,
                'type'      => 'status'
            ],
            'type'          => $type,
            'created_at'    => $comment->created_at,
            'message'       => '<b>' . $commenter->name . '</b>' . ' commented on your status'
        ];

        $data = (new NotificationDto())
                    ->setUserId($comment->commented_user_id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType($type)
                    ->setUploadableId($commenterMedia ? $commenterMedia->uploadable_id : null );
        $notification = Notification::create($data);

        $payload['image'] = $image;
        $payload['id'] = $notification->id;
        $payload['unread_count'] = getUnreadNotification($comment->commented_user_id);
        PushNotificationJob::dispatch('sendBatchNotification', [
            [$token], [
                'topicName'     => 'kizuner',
                'title'         => $notification->title,
                'body'          => $notification->body,
                'payload'       => $payload
            ],
        ]);

        // if ($emailReceiver) {
        //     SysNotification::route('mail', $emailReceiver)
        //         ->notify(new CommentEmail('', $notification->title, $notification->body, $emailReceiver, ""));
        // }
    }
}
