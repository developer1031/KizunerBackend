<?php

namespace Modules\Notification\Job;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\Rating\Domains\Entities\RatingEntity;
use Modules\User\Domains\User;

class NewReviewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $review;

    public function __construct(RatingEntity $review)
    {
       $this->review = $review;
    }

    public function handle()
    {
        $review = $this->review;
        info($review);
        //Get reviewer
        $reviewer = User::find($review->user_id);
        $reviewerMedia = $reviewer->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($reviewerMedia) {
            $image = \Storage::disk('gcs')->url($reviewerMedia->thumb);
        }

        //Prepare message
        $message = $reviewer->name . ' reviewed your hangout';
        $type    = 'new-review';

        $payload = [
            'relation' => [
                'id'        => $review->rating_id,
                'type'      => 'review',
                'user_id'   => $review->user_id
            ],
            'type'          => $type,
            'created_at'    => $review->created_at,
            'message'       => '<b>' . $reviewer->name . '</b>' . ' reviewed your hangout'
        ];


        //save message
        $data = (new NotificationDto())
                    ->setUserId($review->ratted_user_id)
                    ->setTitle('Kizuner')
                    ->setBody($message)
                    ->setPayload($payload)
                    ->setType($type)
                    ->setUploadableId(!isset($reviewerMedia) ? null : $reviewerMedia->uploadable_id);
        $notification = Notification::create($data);

        //Push notification
        $token = UserDeviceToken::getUserDevice($review->ratted_user_id, '');

        if ($token) {
            $payload['image'] = $image;
            $payload['id'] = $notification->id;
            $payload['unread_count'] = getUnreadNotification($review->ratted_user_id);
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
