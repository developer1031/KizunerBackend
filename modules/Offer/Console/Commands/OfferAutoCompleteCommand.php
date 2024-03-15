<?php

namespace Modules\Offer\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Kizuner\Models\Offer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Job\Hangout\HangoutCompletedJob;
use Modules\Notification\Job\Reminder\RemindOfferReview;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\User\Notifications\HangoutHelpEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as SysNotification;

class OfferAutoCompleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Complete Finish Offer and Fire event to transfer Money';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::Info("OfferAutoCompleteCommand running");

        try {

            $currentTime = Carbon::now();
            $getHelpOffers = Offer::where('status', Offer::$status['paid'])->whereNotNull('end')->where('end', '<=', $currentTime)->get();

            $casts_sent_notification = [];
            foreach ($getHelpOffers as $offer) {
                $offer->status = Offer::$status['completed'];

                $hangout = $offer->hangout;

                if (!$hangout) {
                    continue;
                }

                $hangout->is_completed = 1;
                $hangout->save();

                if ($hangout->is_min_capacity && intval($hangout->is_min_capacity) > 0) {
                    $offers_number = $hangout->offer_accepted;
                    if ($offers_number < intval($hangout->is_min_capacity)) {
                        try {
                            $message = 'The offers is not reach to minimum. The minimum is: ' . $hangout->is_min_capacity;
                            $type = 'offer_complete';
                            $payload = [
                                'relation' => [
                                    'id' => $hangout->id,
                                    'type' => 'hangout'
                                ],
                                'type' => $type,
                                'created_at' => $offer->created_at,
                                'message' => $message
                            ];

                            //Sender
                            $data = (new NotificationDto())
                                ->setUserId($offer->sender_id)
                                ->setTitle('Kizuner')
                                ->setBody($message)
                                ->setPayload($payload)
                                ->setType($type)
                                ->setUploadableId(null);

                            $notification = Notification::create($data);
                            $token = UserDeviceToken::getUserDevice($offer->sender_id, "hangout_help_notification");
                            if ($token) {
                                $payload['image'] = null;
                                $payload['id'] = $notification->id;
                                $payload['unread_count'] = getUnreadNotification($offer->sender_id);
                                PushNotificationJob::dispatch('sendBatchNotification', [
                                    [$token], [
                                        'topicName' => 'kizuner',
                                        'title' => $notification->title,
                                        'body' => $notification->body,
                                        'payload' => $payload
                                    ],
                                ]);
                            }

                            // $emailReceiver = UserDeviceToken::getUserEmail($offer->sender_id, "hangout_help_notification");
                            // if ($emailReceiver) {
                            //     SysNotification::route('mail', $emailReceiver)
                            //         ->notify(new HangoutHelpEmail('kizuner', $notification->title, $notification->body, $emailReceiver, ""));
                            // }

                            //Receiver
                            if (isset($casts_sent_notification[$hangout->id]) && $casts_sent_notification[$hangout->id] == $offer->receiver_id) {
                            } else {
                                $data = (new NotificationDto())
                                    ->setUserId($offer->receiver_id)
                                    ->setTitle('Kizuner')
                                    ->setBody($message)
                                    ->setPayload($payload)
                                    ->setType($type)
                                    ->setUploadableId(null);
                                $notification = Notification::create($data);
                                $token = UserDeviceToken::getUserDevice($offer->receiver_id, "hangout_help_notification");
                                if ($token) {
                                    $payload['image'] = null;
                                    $payload['id'] = $notification->id;
                                    $payload['unread_count'] = getUnreadNotification($offer->receiver_id);
                                    PushNotificationJob::dispatch('sendBatchNotification', [
                                        [$token], [
                                            'topicName' => 'kizuner',
                                            'title' => $notification->title,
                                            'body' => $notification->body,
                                            'payload' => $payload
                                        ],
                                    ]);
                                }
                                // $emailReceiver = UserDeviceToken::getUserEmail($offer->receiver_id, "hangout_help_notification");
                                // if ($emailReceiver) {
                                //     SysNotification::route('mail', $emailReceiver)
                                //         ->notify(new HangoutHelpEmail('kizuner', $notification->title, $notification->body, $emailReceiver, ""));
                                // }
                                $casts_sent_notification[$hangout->id] = $offer->receiver_id;
                            }

                            $offer->status = Offer::$status['reject'];
                            $offer->save();

                            $offer->save();
                        } catch (\Exception $e) {
                            Log::error("OfferAutoCompleteCommand Error: " . $e->getMessage());
                        }
                        continue;
                    }
                }

                $offer->save();
                RemindOfferReview::dispatch($offer);

                //Notificate to user help
                HangoutCompletedJob::dispatch($offer->hangout, $offer);
            }
        } catch (\Exception $e) {

            Log::error("OfferAutoCompleteCommand Error: " . $e->getMessage());
        }
    }
}
