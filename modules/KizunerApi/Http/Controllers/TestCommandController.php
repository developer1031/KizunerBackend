<?php

namespace Modules\KizunerApi\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Helps\Models\HelpOffer;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\Notification;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Job\Help\HelpCompletedJob;
use Modules\Notification\Job\Reminder\RemindOfferReview;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Transaction;
use Modules\Wallet\Domains\Wallet;
use Symfony\Component\HttpFoundation\Response;

class TestCommandController
{
    public function testHelpOfferAutoCompleteCommand()
    {
        $currentTime = Carbon::now();
        Log::Info("HelpOfferAutoCompleteCommand running");

        //Count all duplicate offer
        $offer_count = [];
        $getHelpOffers = HelpOffer::where('status', HelpOffer::$status['accept'])->where('end', '<=', $currentTime)->get();
        foreach ($getHelpOffers as $getHelpOffer) {
            if(!isset($offer_count[$getHelpOffer->help_id]))
                $offer_count[$getHelpOffer->help_id] = 1;
            else
                $offer_count[$getHelpOffer->help_id] += 1;
        }

        foreach ($getHelpOffers as $offer) {

            try {
                $offer->status = HelpOffer::$status['approved'];
                // dump("Processing ID: " . $offer->id . " - title: " . $offer->hangout_title);

                $senderWallet       = Wallet::findByUserId($offer->sender_id);
                $receiverWallet     = Wallet::findByUserId($offer->receiver_id);

                // Move Money
                //$div = (isset($offer_count[$offer->help_id]) && $offer_count[$offer->help_id]) ? $offer_count[$offer->help_id] : 1;
                $help = $offer->help;
                $div = ($help && $help->offer_accepted) ? $help->offer_accepted : 1;

                //dump($senderWallet->id);
                //dd((ceil(($offer->kizuna)/$div)));

                if($help->is_min_capacity && intval($help->is_min_capacity) > 0) {
                    $offers_number = $help->offer_accepted;
                    $help->is_completed = 1;
                    $help->save();

                    if($offers_number < intval($help->is_min_capacity)) {
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
                            $token = UserDeviceToken::getUserDevice($offer->sender_id, '');
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
                            $token = UserDeviceToken::getUserDevice($offer->receiver_id, '');
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

                            //Refund KZ to Requester
                            if(!$help->is_refund) {
                                $userBalance = Wallet::findByUserId($help->user_id);
                                $budget = floatval($help->budget) * intval($help->capacity);
                                Wallet::updateBalance($userBalance->id, +$budget);
                                History::create(new HistoryDto(
                                    $help->user_id,
                                    $help->user_id,
                                    $help->id,
                                    HistoryEntity::TYPE_REFUND_OFFER,
                                    HistoryEntity::BALANCE_ADD,
                                    0,
                                    $budget
                                ));
                                $help->is_refund = 1;
                                $help->save();
                            }
                        }
                        catch (\Exception $e) {}
                        continue;
                    }
                }

                $amount = ceil($offer->kizuna);
                Wallet::updateBalance( $senderWallet->id, $amount );

                Transaction::create(
                    $offer->sender_id,
                    $offer->receiver_id,
                    0,
                    TransactionEntity::TYPE_OFFER,
                    $amount,
                );

                History::create(new HistoryDto(
                    $offer->sender_id,
                    $offer->receiver_id,
                    $offer->id,
                    HistoryEntity::TYPE_OFFER,
                    HistoryEntity::BALANCE_ADD,
                    0,
                    $amount
                ));

                //-kz for receiver
                //Update History for Pending Kz
                $pending_history = HistoryEntity::where('ref_user_id', $help->user_id)->where('ref_id', $help->id)->first();
                if($pending_history) {
                    $pending_history->ref_user_id = $offer->sender_id;
                    $pending_history->ref_id = $offer->sender_id;
                    $pending_history->type = HistoryEntity::TYPE_ADVANCE_COMPLETE_OFFER;
                    $pending_history->save();
                }

                //Update Completed offer number
                $help->offer_completed = $help->offer_completed + 1;
                $help->save();

                $offer->save();
                RemindOfferReview::dispatch($offer);

                //Notificate to user help
                HelpCompletedJob::dispatch($offer->help, $offer);

                // + Point to leaderBoard
                $helper = User::where('id', $offer->sender_id)->first();
                addPoint(1, $helper);

                //Refund remains
            }
            catch(Exception $e) {}


        }

        return new JsonResponse(
            Response::HTTP_CREATED
        );
    }
}
