<?php

namespace Modules\KizunerApi\Services\Hangout;

use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Contracts\HangoutRepositoryInterface;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Contracts\OfferRepositoryInterface;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\KizunerApi\Http\Requests\Hangout\OfferRequest;
use Modules\KizunerApi\Http\Requests\Hangout\OfferStatusChangeRequest;
use Modules\KizunerApi\Transformers\OfferTransform;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Job\NewOfferJob;
use Modules\Notification\Job\OfferAcceptedJob;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Transaction;
use Modules\Wallet\Services\NowManager;
use Modules\Wallet\Services\StripeManager;
use Modules\Wallet\Domains\Wallet;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\PaymentEmail;

class OfferManager
{

    /** @var OfferRepositoryInterface */
    private $offerRepository;

    /** @var HangoutRepositoryInterface  */
    private $hangoutRepository;

    private $stripeManager;
    private $nowManager;

    /** @var MediaRepositoryInterface */
    private $mediaRepository;

    /**
     * OfferManager constructor.
     * @param OfferRepositoryInterface $offerRepository
     * @param HangoutRepositoryInterface $hangoutRepository
     */
    public function __construct(
        OfferRepositoryInterface $offerRepository,
        HangoutRepositoryInterface $hangoutRepository,
        MediaRepositoryInterface $mediaRepository,
        StripeManager $stripeManager,
        NowManager $nowManager
    ) {
        $this->offerRepository = $offerRepository;
        $this->hangoutRepository = $hangoutRepository;
        $this->mediaRepository = $mediaRepository;
        $this->stripeManager = $stripeManager;
        $this->nowManager = $nowManager;
    }

    /**
     * @param OfferRequest $request
     * @return \Spatie\Fractal\Fractal
     * @throws PermissionDeniedException
     */
    public function offerHangout(OfferRequest $request)
    {
        $data['sender_id'] = app('request')->user()->id;
        $data['hangout_id'] = $request->get('hangout_id');

        $hangout = $this->hangoutRepository->get($data['hangout_id']);

        $isWaiting = $this->offerRepository->isWaiting($data['sender_id'], $data['hangout_id']);

        if ($data['sender_id'] == $hangout->user_id) {
            throw new PermissionDeniedException('You can not offer this Hangout ');
        }

        if ($isWaiting) {
            $isWaiting->status = Offer::$status['cancel'];
            $isWaiting->save();
        }

        $isCancelled = $this->offerRepository->isCancelledHangout($data['sender_id'], $data['hangout_id']);

        if (
            !$isWaiting &&
            ($hangout->capacity == null ||
                ($hangout->available > 0 && $hangout->available != null))
        ) {
            $data['status'] = Offer::$status['pending'];
            $hangout->update([
                'available' => ($hangout->capacity == null) ? $hangout->available : $hangout->available - 1
            ]);
        } elseif ($hangout->available == 0) {
            if (!$isWaiting) {
                $data['status'] = Offer::$status['queuing'];
            } else {
                $hangout->update([
                    'available' => $hangout->available + 1
                ]);
            }
        }

        $data['kizuna'] = $hangout->kizuna;
        $data['hangout_title'] = $hangout->title;
        $data['address'] = $hangout->address;
        $data['start'] = $hangout->start;
        $data['end'] = $hangout->end;
        $data['receiver_id'] = $hangout->user_id;
        $data['position'] = $hangout->offers->count() == 0 ? 1 : $hangout->offers->count() + 1;
        $data['hangout_update'] = $hangout->updated_at;
        $data['address'] = $hangout->location == null ? null : $hangout->location->address;
        $data['amount'] = $hangout->amount;

        /** @var Offer $offer */

        if ($isCancelled) {
            $offer = $this->offerRepository->update($isCancelled->id, $data);
            NewOfferJob::dispatch($offer);
        } else {
            $offer = $this->offerRepository->create($data);
            //Send Noti new offer
            NewOfferJob::dispatch($offer);
        }

        return fractal($offer, new OfferTransform());
    }

    /**
     * @param OfferStatusChangeRequest $request
     * @param string $id
     * @return \Spatie\Fractal\Fractal
     * @throws InCorrectFormatException
     */
    public function changeStatus(OfferStatusChangeRequest $request, string $id)
    {
        $offer = Offer::find($id);
        $hangout = $offer->hangout;

        switch ($request->get('status')) {
            case 'accept':
                if ($offer->status != Offer::$status['pending']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->save();

                OfferAcceptedJob::dispatch($offer);
                $hangout->offer_accepted = intval($hangout->offer_accepted) + 1;
                $hangout->save();

                break;
            case 'declined':
            case 'guest_declined':
                if ($offer->status != Offer::$status['pending']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->save();

                // Check hangout have queuing left
                $nextOffer = $hangout->offers()
                    ->where('status', Offer::$status['queuing'])
                    ->orderBy('position')
                    ->first();
                if (!$offer->is_refund) {
                    // TODO handle refund later
                    // $refund = $this->walletManager->refund(
                    //     new RefundDto(
                    //         $offer->payment_method,
                    //         $offer->stripe_intent_id ?? '',
                    //         $offer->refund_crypto_wallet_id ?? '',
                    //         $offer->amount
                    //     )
                    // );

                    // switch ($offer->payment_method) {
                    //     case Hangout::PAYMENT_METHOD_CREDIT:
                    //         $offer->stripe_refund_id = $refund->id;
                    //         $offer->payment_status = Offer::PAYMENT_STATUS_REFUNDED;
                    //         break;

                    //     case Hangout::PAYMENT_METHOD_CRYPTO:
                    //         $offer->now_payments_refund_id = $refund->id;
                    //         $offer->payment_status = $this->walletManager->getIsNowPaymentSandbox() ? Offer::PAYMENT_STATUS_REFUNDED : Offer::PAYMENT_STATUS_REFUNDING;
                    //         break;
                    // }

                    $offer->is_refund = true;
                    $offer->save();
                }

                $hangout->save();

                if ($nextOffer) {
                    $nextOffer->update([
                        'status' => Offer::$status['pending']
                    ]);
                } else {
                    $hangout->update([
                        'available' => $hangout->available + 1
                    ]);
                }
                break;
            case 'paid':
                if ($offer->status != Offer::$status['accept']) {
                // if ($offer->status != Offer::$status['accept'] || $offer->payment_status != null) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->payment_method = $request->get('payment_method');

                if (
                    $hangout->payment_method !== Hangout::PAYMENT_METHOD_BOTH &&
                    $offer->payment_method !== $hangout->payment_method
                ) {
                    throw new InCorrectFormatException('This payment method is not allowed');
                }

                $offer->payment_status = Offer::PAYMENT_STATUS_UNPAID;
                $offer->save();

                switch ($offer->payment_method) {
                    case Hangout::PAYMENT_METHOD_CREDIT:

                        $paymentRes = $this->stripeManager->payment(
                            $request->get('card_id'),
                            $hangout->amount,
                            'Payment for hangout: ' . $hangout->title
                        );

                        $offer->stripe_intent_id = $paymentRes->id;
                        $offer->payment_status = Offer::PAYMENT_STATUS_PAID;
                        $offer->status = Offer::$status[$request->get('status')];
                        $offer->save();

                        break;

                    case Hangout::PAYMENT_METHOD_CRYPTO:
                        $paymentRes = $this->nowManager->payment(
                            $hangout->amount,
                            $request->get('currency')
                        );

                        $offer->now_payments_id = $paymentRes->id;
                        $offer->invoice_url = $paymentRes->invoice_url;
                        $offer->refund_crypto_wallet_id = $request->get('refund_crypto_wallet_id');

                        if ($this->nowManager->getIsSandbox()) {
                          $offer->payment_status = Offer::PAYMENT_STATUS_PAID;
                          $offer->status = Offer::$status[$request->get('status')];
                        }

                        $offer->save();

                        break;
                }

                History::create(
                    new HistoryDto(
                        auth()->user()->id, //Sender
                        auth()->user()->id,
                        $hangout->id,
                        HistoryEntity::TYPE_ADVANCE,
                        HistoryEntity::BALANCE_MINUS,
                        0,
                        $hangout->amount
                    )
                );

                break;
            case 'started':
                if ($offer->status != Offer::$status['paid']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->save();

                break;
            case 'guest_started':
                if ($offer->status != Offer::$status['paid']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $token = UserDeviceToken::getUserDevice($offer->receiver_id, "hangout_help_notification");

                if ($token) {
                    $message = 'need to start hangout';
                    $type = 'hangout_required_start';

                    $payload = [
                        'relation' => [
                            'id' => $offer->help_id,
                            'type' => 'hangout'
                        ],
                        'type' => $type,
                        'created_at' => $offer->created_at,
                        'message' => 'need to start hangout'
                    ];

                    $data = (new NotificationDto())
                        ->setUserId($offer->receiver_id)
                        ->setTitle('Kizuner')
                        ->setBody($message)
                        ->setPayload($payload)
                        ->setType($type);


                    $notification = \Modules\Notification\Domains\Notification::create($data);

                    $payload['image'] = null;
                    $payload['id'] = $notification->id;
                    $payload['unread_count'] = getUnreadNotification($offer->receiver_id);
                    PushNotificationJob::dispatch('sendBatchNotification', [
                        [$token],
                        [
                            'topicName' => 'kizuner',
                            'title' => $notification->title,
                            'body' => $notification->body,
                            'payload' => $payload
                        ],
                    ]);
                }
                break;
            case 'cancel':
            case 'cast_cancelled':
                if (
                    $offer->status != Offer::$status['accept'] && 
                    $offer->status != Offer::$status['paid'] && 
                    $offer->status != Offer::$status['started'] && 
                    $offer->status != Offer::$status['guest_started']
                    ) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->is_within_time = $request->get('is_within_time');
                $offer->is_able_contact = $request->get('is_able_contact');
                $offer->save();

                if ($request->get('media_evidence')) {
                    $offer->media_evidence = $request->get('media_evidence');
                    $offer->save();
                    // $cover = $request->get('media_evidence');
                    // if (str_contains($cover, ';')) {
                    //     $coverArr = explode(";", $cover);
                    //     foreach ($coverArr as $coverArrItem) {
                    //         $media = $this->mediaRepository->update($coverArrItem, [
                    //             'type' => Media::$type['hangoutOffer']['cover']
                    //         ]);
                    //         $offer->media()->save($media);
                    //     }
                    // }
                }

                break;
            case 'completed':
                if ($offer->status != Offer::$status['started'] && $offer->status != Offer::$status['guest_started']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->save();

                break;
            case 'approved':
                $offer->payment_status = Offer::PAYMENT_STATUS_TRANSFERRING;
                $offer->save();

                switch ($offer->payment_method) {
                    case Hangout::PAYMENT_METHOD_CREDIT:
                        $transferRes = $this->stripeManager->transfer(
                            $offer->receiver_id,
                            $offer->amount,
                            'Transfer for hangout: ' . $hangout->title
                        );

                        $offer->stripe_transfer_id = $transferRes->id;
                        $offer->payment_status = Offer::PAYMENT_STATUS_TRANSFERRED;
                        $offer->status = Offer::$status[$request->get('status')];
                        $offer->save();

                        break;

                    case Hangout::PAYMENT_METHOD_CRYPTO:
                        $transferRes = $this->nowManager->transfer(
                            $offer->amount,
                            $hangout->crypto_wallet_id
                        );

                        $offer->now_payments_transfer_id = $transferRes->id;

                        if ($this->nowManager->getIsSandbox()) {
                            $offer->payment_status = Offer::PAYMENT_STATUS_TRANSFERRED;
                            $offer->status = Offer::$status[$request->get('status')];
                        }

                        $offer->save();

                        break;
                }

                $castEmail = UserDeviceToken::getUserEmail($offer->sender_id, "email_notification");
                $guestEmail = UserDeviceToken::getUserEmail($offer->receiver_id, "email_notification");
                if ($castEmail) {
                  SysNotification::route('mail', $castEmail)
                  ->notify(new PaymentEmail('','Kizuner Payment Notification', 'Payment Action Success!', $castEmail, ""));
                }
                if ($guestEmail) {
                  SysNotification::route('mail', $guestEmail)
                  ->notify(new PaymentEmail('','Kizuner Payment Notification', 'Payment Action Success!', $guestEmail, ""));
                }

                Transaction::create(
                    $offer->sender_id,
                    $offer->receiver_id,
                    0,
                    TransactionEntity::TYPE_OFFER,
                    $offer->amount
                );

                History::create(
                    new HistoryDto(
                        $offer->receiver_id,
                        $offer->sender_id,
                        $offer->id,
                        HistoryEntity::TYPE_OFFER,
                        HistoryEntity::BALANCE_ADD,
                        0,
                        $offer->amount
                    )
                );

                //Update History for Pending Kz
                $pending_history = HistoryEntity::where('ref_user_id', $offer->sender_id)->where('ref_id', $hangout->id)->first();
                if ($pending_history) {
                    $pending_history->ref_user_id = $offer->receiver_id;
                    $pending_history->ref_id = $offer->receiver_id;
                    $pending_history->type = HistoryEntity::TYPE_ADVANCE_COMPLETE_OFFER;
                    $pending_history->save();
                }

                break;
            case 'reject':
                if ($offer->status != Offer::$status['completed']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $offer->status = Offer::$status[$request->get('status')];
                $offer->subject_reject = $request->get('subject_reject');
                $offer->message_reject = $request->get('message_reject');
                $offer->save();

                if ($request->get('media_evidence')) {
                    $offer->media_evidence = $request->get('media_evidence');
                    $offer->save();
                    // $cover = $request->get('media_evidence');
                    // if (str_contains($cover, ';')) {
                    //     $coverArr = explode(";", $cover);
                    //     foreach ($coverArr as $coverArrItem) {
                    //         $media = $this->mediaRepository->update($coverArrItem, [
                    //             'type' => Media::$type['hangoutOffer']['cover']
                    //         ]);
                    //         $offer->media()->save($media);
                    //     }
                    // }
                }
                break;
            default:
                Log::debug("Invalid status: " . $request->get('status'));
                throw new InCorrectFormatException('Invalid status');
                break;
        }

        return fractal($offer, new OfferTransform());
    }

    public function getOffers(string $id)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $status = 'pending';

        if (app('request')->input('status') === 'waiting') {
            $status = 'queuing';
        }

        $hangout = $this->hangoutRepository->get($id);

        if ($hangout->user->id != app('request')->user()->id) {
            throw new PermissionDeniedException("You don't have permission to see this hangout offers");
        }

        if ($status !== 'queuing') {
            $offers = $hangout->offers()
                // ->whereIn('status', [
                //     Offer::$status[$status],
                //     Offer::$status['accept'],
                //     Offer::$status['completed']
                // ])
                ->whereNotIn('status', [
                    Offer::$status['cancel'],
                    Offer::$status['reject']
                ])
                ->paginate($perPage);
        } else {
            //$offers = $hangout->offers()->where('status', Offer::$status['queuing'])->paginate($perPage);
            $offers = $hangout->offers()
                ->whereIn('status', [
                    Offer::$status['pending'],
                    Offer::$status['queuing'],
                ])
                ->paginate($perPage);
        }
        return fractal($offers, new OfferTransform())->addMeta(
            ['capacity' => $hangout->capacity]
        );
    }
}
