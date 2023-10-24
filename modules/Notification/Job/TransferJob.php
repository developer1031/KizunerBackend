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
use Modules\User\Domains\User;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\Wallet;

class TransferJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TYPE = 'new-transfer';

    private $transaction;

    public function __construct(TransactionEntity $transaction)
    {
        $this->transaction = $transaction;
    }

    public function handle()
    {
        $transaction = $this->transaction;
        $wallet = Wallet::find($transaction->sender);
        $sender = User::find($wallet->user_id);

        $senderMedia = $sender->medias()->where('type', 'user.avatar')->first();
        $image = null;
        if ($senderMedia) {
            $image = \Storage::disk('gcs')->url($senderMedia->thumb);
        }

        $message = 'You received ' . $transaction->point . ' kizuna from ' . $sender->name;

        $payload = [
            'relation' => [
                'id'        => $transaction->id,
                'type'      => 'transaction'
            ],
            'type'          => self::TYPE,
            'created_at'    => $transaction->created_at,
            'message'       => 'You received ' . $transaction->point . ' kizuna from <b>' . $sender->name . '</b>'
        ];
        $wallet = Wallet::find($transaction->receiver);

        $data = (new NotificationDto())
            ->setUserId($wallet->user_id)
            ->setTitle('Kizuner')
            ->setBody($message)
            ->setPayload($payload)
            ->setType(self::TYPE)
            ->setUploadableId($senderMedia ? $senderMedia->uploadable_id : null);
        $notification = Notification::create($data);

        $token = UserDeviceToken::getUserDevice($wallet->user_id, '');

        if ($token) {
            $payload['image'] = $image;
            $payload['id'] = $notification->id;
            $payload['unread_count'] = getUnreadNotification($wallet->user_id);
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
