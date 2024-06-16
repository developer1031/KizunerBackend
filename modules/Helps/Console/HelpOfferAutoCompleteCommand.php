<?php

namespace Modules\Helps\Console;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Helps\Models\HelpOffer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Job\Help\HelpCompletedJob;
use Modules\Notification\Job\Reminder\RemindOfferReview;
use Modules\Notification\Notification\PushNotificationJob;

class HelpOfferAutoCompleteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'help_offers:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Complete Finish Help Offer and Fire event to transfer Money';

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
        $currentTime = Carbon::now();
        Log::Info("HelpOfferAutoCompleteCommand running");

        //Count all duplicate offer
        $offer_count = [];
        $getHelpOffers = HelpOffer::where('status', HelpOffer::$status['paid'])->where('end', '<=', $currentTime)->get();
        foreach ($getHelpOffers as $getHelpOffer) {
            if (!isset($offer_count[$getHelpOffer->help_id]))
                $offer_count[$getHelpOffer->help_id] = 1;
            else
                $offer_count[$getHelpOffer->help_id] += 1;
        }

        foreach ($getHelpOffers as $offer) {

            try {
                $offer->status = HelpOffer::$status['completed'];

                $help = $offer->help;

                if (!$help) {
                    continue;
                }

                if ($help->is_min_capacity && intval($help->is_min_capacity) > 0) {
                    $offers_number = $help->offer_accepted;
                    $help->is_completed = 1;
                    $help->save();

                    if ($offers_number < intval($help->is_min_capacity)) {
                        try {
                            $message = 'The offers is not reach to minimum. The minimum is: ' . $help->is_min_capacity;
                            $type    = 'offer_complete';
                            $payload = [
                                'relation' => [
                                    'id'        => $help->id,
                                    'type'      => 'help'
                                ],
                                'type'          => $type,
                                'created_at'    => $offer->created_at,
                                'message'       => $message
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
                            $token = UserDeviceToken::getUserDevice($offer->sender_id, 'hangout_help_notification');


                            if ($token) {
                                $payload['image'] = null;
                                $payload['id'] = $notification->id;
                                $payload['unread_count'] = getUnreadNotification($offer->sender_id);
                                PushNotificationJob::dispatch('sendBatchNotification', [
                                    [$token], [
                                        'topicName'     => 'kizuner',
                                        'title'         => $notification->title,
                                        'body'          => $notification->body,
                                        'payload'       => $payload
                                    ],
                                ]);
                            }

                            //Receiver
                            $data = (new NotificationDto())
                                ->setUserId($offer->receiver_id)
                                ->setTitle('Kizuner')
                                ->setBody($message)
                                ->setPayload($payload)
                                ->setType($type)
                                ->setUploadableId(null);
                            $notification = Notification::create($data);
                            $token = UserDeviceToken::getUserDevice($offer->receiver_id, 'hangout_help_notification');
                            if ($token) {
                                $payload['image'] = null;
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
                            }
                            $offer->status = HelpOffer::$status['reject'];
                            $offer->save();
                        } catch (\Exception $e) {
                            Log::error("HelpOfferAutoCompleteCommand Error: " . $e->getMessage());
                        }
                        continue;
                    }
                }

                //Update Completed offer number
                $help->offer_completed = $help->offer_completed + 1;
                $help->save();

                $offer->save();
                RemindOfferReview::dispatch($offer);

                //Notificate to user help
                HelpCompletedJob::dispatch($offer->help, $offer);
            } catch (Exception $e) {
                Log::error("HelpOfferAutoCompleteCommand Error: " . $e->getMessage());
            }
        }
    }
}
