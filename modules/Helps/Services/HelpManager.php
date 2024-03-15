<?php

namespace Modules\Helps\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Feed\Contracts\Repositories\TimelineRepositoryInterface;
use Modules\Feed\Models\Timeline;
use Modules\Helps\Contracts\HelpOfferRepositoryInterface;
use Modules\Helps\Contracts\HelpRepositoryInterface;
use Modules\Helps\Events\HelpCreatedEvent;
use Modules\Helps\Events\HelpCreatedSocketEvent;
use Modules\Helps\Http\Requests\HelpCancelRequest;
use Modules\Helps\Http\Requests\HelpCreateRequest;
use Modules\Helps\Http\Requests\HelpOfferRequest;
use Modules\Helps\Http\Requests\HelpOfferStatusChangeRequest;
use Modules\Helps\Http\Requests\HelpUpdateRequest;
use Modules\Helps\Http\Requests\HelpUpdateStatusRequest;
use Modules\Helps\Models\Help;
use Modules\Helps\Models\HelpOffer;
use Modules\Helps\Transformers\HelpOfferTransform;
use Modules\Helps\Transformers\HelpTransform;
use Modules\Kizuner\Contracts\LocationRepositoryInterface;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\Kizuner\Models\Media;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\Notification\Device\UserDeviceToken;
use Modules\Notification\Domains\NotificationDto;
use Modules\Notification\Job\Help\HelpTagJob;
use Modules\Notification\Job\NewHelpOfferJob;
use Modules\Notification\Notification\PushNotificationJob;
use Modules\Upload\Models\UploadTrash;
use Modules\Wallet\Domains\Dto\HistoryDto;
use Modules\Wallet\Domains\Entities\HistoryEntity;
use Modules\Wallet\Domains\Entities\TransactionEntity;
use Modules\Wallet\Domains\History;
use Modules\Wallet\Domains\Transaction;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Services\NowManager;
use Modules\Wallet\Services\StripeManager;
use Illuminate\Support\Facades\Notification as SysNotification;
use Modules\User\Notifications\PaymentEmail;

class HelpManager
{
    /** @var HelpRepositoryInterface */
    private $helpRepository;

    /** @var LocationRepositoryInterface */
    private $locationRepository;

    /** @var MediaRepositoryInterface */
    private $mediaRepository;

    /** @var SkillRepositoryInterface */
    private $skillRepository;

    /** @var HelpOfferRepositoryInterface */
    private $helpOfferRepository;

    private $feedTimelineRepository;

    private $stripeManager;
    private $nowManager;

    public function __construct(
        HelpRepositoryInterface $helpRepository,
        LocationRepositoryInterface $locationRepository,
        MediaRepositoryInterface $mediaRepository,
        SkillRepositoryInterface $skillRepository,
        HelpOfferRepositoryInterface $helpOfferRepository,
        TimelineRepositoryInterface $feedTimelineRepository,
        StripeManager $stripeManager,
        NowManager $nowManager
    ) {
        $this->helpRepository = $helpRepository;
        $this->locationRepository = $locationRepository;
        $this->mediaRepository = $mediaRepository;
        $this->skillRepository = $skillRepository;
        $this->helpOfferRepository = $helpOfferRepository;
        $this->feedTimelineRepository = $feedTimelineRepository;
        $this->stripeManager = $stripeManager;
        $this->nowManager = $nowManager;
    }

    public function createNewHelp(HelpCreateRequest $request)
    {
        $helpData = $request->all();

        if (!$request->available_status) {
            $helpData['available_status'] = null;
        }
        $helpData['is_min_capacity'] = 0;
        if ($request->has('isMinCapacity') && $request->get('isMinCapacity') !== null) {
            $helpData['is_min_capacity'] = $request->get('isMinCapacity');
        }

        $helpData['user_id'] = app('request')->user()->id;

        $helpData['is_range_price'] = $request->get('is_range_price');
        $helpData['min_amount'] = $request->get('min_amount');
        $helpData['max_amount'] = $request->get('max_amount');

        $help = $this->helpRepository->create($helpData);

        /** Save Location */
        $locationData = $request->all([
            'address',
            'lat',
            'lng',
            'short_address'
        ]);
        $location = $this->locationRepository->create($locationData);
        $help->location()->save($location);

        /** Save Media */
        if ($request->get('cover')) {
            $cover = $request->get('cover');
            if (str_contains($cover, ';')) {
                $coverArr = explode(";", $cover);
                $arrayMedia = array();
                foreach ($coverArr as $coverArrItem) {
                    $media = $this->mediaRepository->update($coverArrItem, [
                        'type' => Media::$type['help']['cover']
                    ]);

                    array_push($arrayMedia, $media);
                    $help->media()->save($media);
                }
            } else {
                $media = $this->mediaRepository->update($request->get('cover'), [
                    'type' => Media::$type['help']['cover']
                ]);
                $arrayMedia = array($media);
                $help->media()->save($media);
            }
        }

        /** Save Skills */
        $help->skills()->sync($request->get('skills'));

        /** Save Categories */
        $help->categories()->sync($request->get('categories'));

        //Tag friends
        $friends = $request->get('friends');
        if ($friends) {
            $help->friends = $friends;
            $help->save();
        }

        try {
            event(new HelpCreatedEvent($help, $request));
            event(new HelpCreatedSocketEvent($help));
        } catch (\Exception $e) {
            Log::info('HelpCreatedEvent -  error: ');
            Log::info($e->getMessage());
        }

        //Send notification
        HelpTagJob::dispatch($help);

        return fractal($help, new HelpTransform(false));
    }

    public function updateNewHelp(string $id, HelpUpdateRequest $request)
    {

        $help = Help::find($id);

        //Check Balance first
        /*
        if(floatval($request->budget) > $help->budget) {
            $userBalance = Wallet::findByUserId(auth()->user()->id);
            if ($userBalance->available < floatval($request->budget)) {
                throw new PermissionDeniedException('You don\'t have enough Kizuna to update this Help');
            }
        }
        */

        /*
        $helpData = $request->all([
            'title', 'description', 'budget', 'start', 'end'
        ]);
        */

        $helpData = $request->all();
        //remove budget
        unset($helpData['budget']);

        if ($request->has('isMinCapacity') && $request->get('isMinCapacity') !== null) {
            $helpData['is_min_capacity'] = $request->get('isMinCapacity');
        }

        $helpData['user_id'] = app('request')->user()->id;
        $help = $this->helpRepository->update($id, $helpData);

        /** Location */
        if ($help->location->address != $request->get('address')) {
            $help->location()->delete();
            $locationData = $request->all([
                'address',
                'lat',
                'lng',
                'short_address'
            ]);
            $location = $this->locationRepository->create($locationData);
            $help->location()->save($location);
        }

        /** Cover */
        if ($request->has('cover')) {
            if ($request->get('cover') == null) {
                if ($help->media != null) {
                    $helpMedias = $help->media;
                    foreach ($helpMedias as $helpMedia) {
                        UploadTrash::create([
                            ['path' => $helpMedia->path],
                            ['path' => $helpMedia->thumb]
                        ]);
                        $helpMedia->delete();
                    }
                }
            } else {
                $cover = $request->get('cover');
                $helpMedias = $help->media;

                if (str_contains($cover, ';')) {
                    $coverArr = explode(";", $cover);

                    if ($helpMedias != []) {
                        foreach ($helpMedias as $helpMedia) {
                            if (!in_array($helpMedia->id, $coverArr)) {
                                UploadTrash::create([
                                    ['path' => $helpMedia->path],
                                    ['path' => $helpMedia->thumb]
                                ]);
                                $helpMedia->delete();
                            }
                        }

                        foreach ($coverArr as $coverArrItem) {
                            $isExist = $helpMedias->first(function ($helpMedia) use ($coverArrItem) {
                                return $helpMedia->id == $coverArrItem;
                            });

                            if ($isExist == null) {
                                $media = $this->mediaRepository->update($coverArrItem, [
                                    'type' => Media::$type['help']['cover']
                                ]);
                                $help->media()->save($media);
                            }
                        }
                    } else {
                        foreach ($coverArr as $coverArrItem) {
                            $media = $this->mediaRepository->update($coverArrItem, [
                                'type' => Media::$type['help']['cover']
                            ]);
                            $help->media()->save($media);
                        }
                    }
                } else {
                    if ($helpMedias != []) {
                        foreach ($helpMedias as $helpMedia) {
                            if ($helpMedia->id != $cover) {
                                UploadTrash::create([
                                    ['path' => $helpMedia->path],
                                    ['path' => $helpMedia->thumb]
                                ]);
                                $helpMedia->delete();
                            }
                        }
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['help']['cover']
                        ]);
                        $help->media()->save($media);
                    } else {
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['help']['cover']
                        ]);
                        $help->media()->save($media);
                    }
                }
            }
        }

        /** Save Skills */
        $help->skills()->sync($request->get('skills'));

        /** Save Categories */
        $help->categories()->sync($request->get('categories'));

        return fractal($help, new HelpTransform());
    }

    /**
     * @param int $userId
     * @return \Spatie\Fractal\Fractal
     */
    public function getHelpByUser(string $userId)
    {
        $perPage = app('request')->get('per_page');
        if (!$perPage) {
            $perPage = 5;
        }
        $helpList = $this->helpRepository->getByUser($userId, $perPage);
        return fractal($helpList, new HelpTransform());
    }

    /**
     * @param int $helpId
     * @return \Spatie\Fractal\Fractal
     */
    public function getHelpDetail(string $helpId)
    {
        $help = $this->helpRepository->get($helpId);
        return fractal($help, new HelpTransform(true));
    }

    /**
     * @param string $id
     * @return array
     */
    public function deleteHelp(string $id)
    {
        $currentUser = app('request')->user()->id;
        $help = $this->helpRepository->isHelpOwner($currentUser, $id);

        if ($help) {
            try {

                //Check has got any Accepted Offers => not allow delete Helps.
                if ($help->countOfferAccepted() > 0) {
                    return [
                        'data' => [
                            'message' => 'Could not delete this help',
                            'status' => true
                        ]
                    ];
                }

                //Refund in Balance (Wallet)
                //Budget = Amount x (capacity - offer_completed)
                $num_of_offer_completed = $help->countOfferCompleted();
                $budget = floatval($help->amount) * (intval($help->capacity) - intval($num_of_offer_completed));

                if ($budget) {
                    // TODO handle refund
                    // $refund = $this->walletManager->refund(
                    //     new RefundDto(
                    //         $help->payment_method,
                    //         $help->stripe_intent_id ?? '',
                    //         $help->refund_crypto_wallet_id ?? '',
                    //         $budget
                    //     )
                    // );

                    // History::create(
                    //     new HistoryDto(
                    //         $help->user_id,
                    //         $help->user_id,
                    //         $help->id,
                    //         HistoryEntity::TYPE_REFUND_ADVANCE,
                    //         HistoryEntity::BALANCE_ADD,
                    //         0,
                    //         $budget
                    //     )
                    // );

                    $help->is_refund = 1;

                    // switch ($help->payment_method) {
                    //     case Help::PAYMENT_METHOD_CREDIT:
                    //         $help->stripe_refund_id = $refund->id;
                    //         $help->payment_status = Help::PAYMENT_STATUS_REFUNDED;
                    //         break;

                    //     case Help::PAYMENT_METHOD_CRYPTO:
                    //         $help->now_payments_refund_id = $refund->id;
                    //         $help->payment_status = $this->walletManager->getIsNowPaymentSandbox() ? Help::PAYMENT_STATUS_REFUNDED : Help::PAYMENT_STATUS_REFUNDING;
                    //         break;
                    // }

                    $help->save();
                }

                $help->delete();
                $help_id = $help->id;
                $this->feedTimelineRepository->deleteByReference($help_id);
                return [
                    'data' => [
                        'message' => 'Delete Help successfully',
                        'status' => true
                    ]
                ];
            } catch (Exception $e) {
                return [
                    'data' => [
                        'message' => $e->getMessage(),
                        'status' => false
                    ]
                ];
            }
        }
    }

    /**
     * The function updates the available status of a help object and returns the updated object.
     * 
     * @param string id The "id" parameter is a string that represents the unique identifier of the
     * help item that needs to be updated. It is used to retrieve the specific help item from the help
     * repository.
     * @param HelpUpdateStatusRequest request The  parameter is an instance of the
     * HelpUpdateStatusRequest class. It is used to retrieve the data sent in the request payload. In
     * this case, it is used to get the value of the 'available_status' field from the request.
     * 
     * @return the updated help object transformed using the HelpTransform class.
     */
    public function updateStatus(string $id, HelpUpdateStatusRequest $request)
    {
        $help = $this->helpRepository->get($id);
        $help->available_status = $request->get('available_status');
        $help->save();
        return fractal($help, new HelpTransform());
    }

    public function requestCancel(string $id, HelpCancelRequest $request)
    {
        $help = $this->helpRepository->get($id);
        if ($help->getOfferAccepted()) {
            return null;
        }
        $help->is_cancel = true;
        $help->is_able_contact = $request->get('is_able_contact');
        $help->save();
        if (!$help->is_refund) {
            $budget = floatval($help->amount) * intval($help->capacity);

            // TODO handle refund
            // $refund = $this->walletManager->refund(
            //     new RefundDto(
            //         $help->payment_method,
            //         $help->stripe_intent_id ?? '',
            //         $help->refund_crypto_wallet_id ?? '',
            //         $budget
            //     )
            // );

            // History::create(new HistoryDto(
            //     $help->user_id,
            //     $help->user_id,
            //     $help->id,
            //     HistoryEntity::TYPE_REFUND_OFFER,
            //     HistoryEntity::BALANCE_ADD,
            //     0,
            //     $budget
            // ));

            // switch ($help->payment_method) {
            //     case Help::PAYMENT_METHOD_CREDIT:
            //         $help->stripe_refund_id = $refund->id;
            //         $help->payment_status = Help::PAYMENT_STATUS_REFUNDED;
            //         break;

            //     case Help::PAYMENT_METHOD_CRYPTO:
            //         $help->now_payments_refund_id = $refund->id;
            //         $help->payment_status = $this->walletManager->getIsNowPaymentSandbox() ? Help::PAYMENT_STATUS_REFUNDED : Help::PAYMENT_STATUS_REFUNDING;
            //         break;
            // }

            $help->is_refund = 1;
            $help->save();
        }
        return fractal($help, new HelpTransform());
    }

    /*
     * Offer
     */
    public function offer(HelpOfferRequest $request)
    {
        $data['sender_id'] = app('request')->user()->id;
        $data['help_id'] = $request->help_id;

        $help = $this->helpRepository->get($data['help_id']);
        if ($data['sender_id'] == $help->user_id) {
            throw new PermissionDeniedException('You can not offer this Help');
        }

        $wallet = Wallet::findByUserId(auth()->user()->id);
        if (
            $help->payment_method == Help::PAYMENT_METHOD_CREDIT &&
            !$wallet->payouts_enabled
        ) {
            throw new PermissionDeniedException('Stripe connect is required');
        }

        $isWaiting = $this->helpOfferRepository->isWaiting($data['sender_id'], $data['help_id']);
        if ($isWaiting) {
            $isWaiting->status = HelpOffer::$status['cancel'];
            $isWaiting->save();
        }

        $data['kizuna'] = $help->budget;
        $data['help_title'] = $help->title;
        $data['address'] = $help->address;
        $data['start'] = $help->start;
        $data['end'] = $help->end;
        $data['receiver_id'] = $help->user_id;
        $data['position'] = $help->offers->count() == 0 ? 1 : $help->offers->count() + 1;
        $data['help_update'] = $help->updated_at;
        $data['address'] = $help->location == null ? null : $help->location->address;
        $data['status'] = HelpOffer::$status['pending'];
        $data['crypto_wallet_id'] = $request->get('crypto_wallet_id');

        /** @var HelpOffer $helpOffer */
        $isCancelled = $this->helpOfferRepository->isCancelledHelp($data['sender_id'], $data['help_id']);
        if ($isCancelled) {
            $offer = $this->helpOfferRepository->update($isCancelled->id, $data);
            NewHelpOfferJob::dispatch($offer);
        } else {
            $offer = $this->helpOfferRepository->create($data);
            //Send Noti new offer
            NewHelpOfferJob::dispatch($offer);
        }

        $help->update([
            'available' => ($help->capacity == null) ? $help->available : $help->available - 1,
        ]);

        return fractal($offer, new HelpOfferTransform());
    }

    /*
     * Get Offer
     */
    public function getOffers(string $id)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $status = 'pending';

        if (app('request')->input('status') === 'waiting') {
            //$status = 'queuing';
            $status = 'pending';
        }

        $help = $this->helpRepository->get($id);
        if ($help->user->id != app('request')->user()->id) {
            throw new PermissionDeniedException("You don't have permission to see this help offers");
        }

        //dd(HelpOffer::$status['queuing']);

        if ($status !== 'queuing') {
            $offers = $help->offers()
              // ->whereIn('status', [
              //     HelpOffer::$status[$status],
              //     HelpOffer::$status['accept'],
              //     HelpOffer::$status['completed']
              // ])
              ->whereNotIn('status', [
                  HelpOffer::$status['cancel'],
                  HelpOffer::$status['reject']
              ])
            ->paginate($perPage);
        } else {
            $offers = $help->offers()->where('status', HelpOffer::$status['queuing'])->paginate($perPage);
        }
        return fractal($offers, new HelpOfferTransform())->addMeta(
            ['capacity' => $help->capacity]
        );
    }

    /**
     * The function `changeStatus` is used to update the status of a help offer and perform various
     * actions based on the new status.
     * 
     * @param HelpOfferStatusChangeRequest request The  parameter is an instance of the
     * HelpOfferStatusChangeRequest class, which is used to retrieve the status value from the request.
     * @param string id The `` parameter is a string that represents the ID of the help offer that
     * needs to have its status changed.
     * 
     * @return the updated HelpOffer object after performing the necessary updates and actions based on
     * the provided status.
     */
    public function changeStatus(HelpOfferStatusChangeRequest $request, string $id)
    {
        $helpOffer = HelpOffer::find($id);
        $help = $helpOffer->help;

        switch ($request->get('status')) {
            case 'accept':
                if ($helpOffer->status != HelpOffer::$status['pending']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                $help->offer_accepted = intval($help->offer_accepted) + 1;
                $help->save();
                break;
            case 'declined':
            case 'helper_declined':
                if ($helpOffer->status != HelpOffer::$status['pending']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                break;
            case 'paid':
                if ($helpOffer->status != HelpOffer::$status['accept'] || $helpOffer->payment_status != null) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->payment_status = HelpOffer::PAYMENT_STATUS_UNPAID;
                $helpOffer->save();

                switch ($help->payment_method) {
                    case Help::PAYMENT_METHOD_CREDIT:
                        $paymentRes = $this->stripeManager->payment(
                            $help->card_id,
                            $help->amount,
                            'Payment for help: ' . $help->title
                        );

                        $helpOffer->stripe_intent_id = $paymentRes->id;
                        $helpOffer->payment_status = HelpOffer::PAYMENT_STATUS_PAID;
                        $helpOffer->status = HelpOffer::$status[$request->get('status')];
                        $helpOffer->save();

                        break;
                    case Help::PAYMENT_METHOD_CRYPTO:
                        $paymentRes = $this->nowManager->payment(
                            $help->amount,
                            $help->currency
                        );

                        $helpOffer->now_payments_id = $paymentRes->id;
                        $helpOffer->invoice_url = $paymentRes->invoice_url;
                        $helpOffer->save();

                        break;
                }

                History::create(
                    new HistoryDto(
                        $help->user_id,
                        $help->user_id,
                        $help->id,
                        HistoryEntity::TYPE_ADVANCE,
                        HistoryEntity::BALANCE_MINUS,
                        0,
                        $help->amount
                    )
                );
                break;
            case 'started':
                if ($helpOffer->status != HelpOffer::$status['paid']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                break;
            case 'helper_started':
                if ($helpOffer->status != HelpOffer::$status['paid']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $token = UserDeviceToken::getUserDevice($helpOffer->receiver_id, "hangout_help_notification");

                if ($token) {
                    $message = 'need to start help';
                    $type = 'help_required_start';

                    $payload = [
                        'relation' => [
                            'id' => $helpOffer->help_id,
                            'type' => 'help'
                        ],
                        'type' => $type,
                        'created_at' => $helpOffer->created_at,
                        'message' => 'need to start help'
                    ];

                    $data = (new NotificationDto())
                        ->setUserId($helpOffer->receiver_id)
                        ->setTitle('Kizuner')
                        ->setBody($message)
                        ->setPayload($payload)
                        ->setType($type);


                    $notification = \Modules\Notification\Domains\Notification::create($data);

                    $payload['image'] = null;
                    $payload['id'] = $notification->id;
                    $payload['unread_count'] = getUnreadNotification($helpOffer->receiver_id);
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
                if (
                    $helpOffer->status != HelpOffer::$status['accept'] &&
                    $helpOffer->status != HelpOffer::$status['paid'] &&
                    $helpOffer->status != HelpOffer::$status['started'] &&
                    $helpOffer->status != HelpOffer::$status['helper_started']
                ) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                // Check help have queuing left
                $nextOffer = $help->offers()
                    ->where('status', HelpOffer::$status['queuing'])
                    ->orderBy('position')
                    ->first();

                if ($nextOffer) {
                    $nextOffer->update([
                        'status' => HelpOffer::$status['pending']
                    ]);
                } else {
                    $help->update([
                        'available' => $help->available + 1
                    ]);
                }

                $helpOffer->is_within_time = $request->get('is_within_time');
                $helpOffer->is_able_contact = $request->get('is_able_contact');
                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                if ($request->get('media_evidence')) {
                    $helpOffer->media_evidence = $request->get('media_evidence');
                    $helpOffer->save();
                    // $cover = $request->get('media_evidence');
                    // if (str_contains($cover, ';')) {
                    //     $coverArr = explode(";", $cover);
                    //     foreach ($coverArr as $coverArrItem) {
                    //         $media = $this->mediaRepository->update($coverArrItem, [
                    //             'type' => Media::$type['helpOffer']['cover']
                    //         ]);
                    //         $helpOffer->media()->save($media);
                    //     }
                    // }
                }

                break;
            case 'helper_cancelled':
                if (
                    $helpOffer->status != HelpOffer::$status['accept'] &&
                    $helpOffer->status != HelpOffer::$status['paid'] &&
                    $helpOffer->status != HelpOffer::$status['started'] &&
                    $helpOffer->status != HelpOffer::$status['helper_started']
                ) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->is_within_time = $request->get('is_within_time');
                $helpOffer->is_able_contact = $request->get('is_able_contact');
                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                if ($request->get('media_evidence')) {
                    $helpOffer->media_evidence = $request->get('media_evidence');
                    $helpOffer->save();
                    // $cover = $request->get('media_evidence');
                    // if (str_contains($cover, ';')) {
                    //     $coverArr = explode(";", $cover);
                    //     foreach ($coverArr as $coverArrItem) {
                    //         $media = $this->mediaRepository->update($coverArrItem, [
                    //             'type' => Media::$type['helpOffer']['cover']
                    //         ]);
                    //         $helpOffer->media()->save($media);
                    //     }
                    // }
                }

                break;
            case 'completed':
                if ($helpOffer->status != HelpOffer::$status['started'] && $helpOffer->status != HelpOffer::$status['helper_started']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                break;
            case 'reject':
                if ($helpOffer->status != HelpOffer::$status['completed']) {
                    throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                }

                $helpOffer->status = HelpOffer::$status[$request->get('status')];
                $helpOffer->save();

                // Check help have queuing left
                $help = $helpOffer->help;
                $nextOffer = $help->offers()
                    ->where('status', HelpOffer::$status['queuing'])
                    ->orderBy('position')
                    ->first();

                if ($nextOffer) {
                    $nextOffer->update([
                        'status' => HelpOffer::$status['pending']
                    ]);
                } else {
                    $help->update([
                        'available' => $help->available + 1
                    ]);
                }

                break;
            case 'approved':
                $helpOffer->payment_status =  HelpOffer::PAYMENT_STATUS_TRANSFERRING;
                $helpOffer->save();

                //Complete Help manually
                switch ($help->payment_method) {
                    case Help::PAYMENT_METHOD_CREDIT:
                        $transferRes = $this->stripeManager->transfer(
                            $helpOffer->sender_id,
                            $help->amount,
                            'Payment for help: ' . $help->title
                        );

                        $helpOffer->stripe_transfer_id = $transferRes->id;
                        $helpOffer->payment_status = HelpOffer::PAYMENT_STATUS_TRANSFERRED;
                        $helpOffer->status = HelpOffer::$status[$request->get('status')];
                        $helpOffer->save();

                        break;
                    case Help::PAYMENT_METHOD_CRYPTO:
                        $paymentRes = $this->nowManager->transfer(
                            $help->amount,
                            $helpOffer->crypto_wallet_id
                        );
                        $helpOffer->now_payments_transfer_id = $paymentRes->id;

                        if ($this->nowManager->getIsSandbox()) {
                            $helpOffer->payment_status = HelpOffer::PAYMENT_STATUS_TRANSFERRED;
                            $helpOffer->status = HelpOffer::$status[$request->get('status')];
                        }

                        $helpOffer->save();

                        break;
                }

                $helperEmail = UserDeviceToken::getUserEmail($helpOffer->sender_id, "email_notification");
                $requesterEmail = UserDeviceToken::getUserEmail($helpOffer->receiver_id, "email_notification");
                if ($helperEmail) {
                  SysNotification::route('mail', $helperEmail)
                  ->notify(new PaymentEmail('','Kizuner Payment Notification', 'Payment Action Success!', $helperEmail, ""));
                }
                if ($requesterEmail) {
                  SysNotification::route('mail', $requesterEmail)
                  ->notify(new PaymentEmail('','Kizuner Payment Notification', 'Payment Action Success!', $requesterEmail, ""));
                }

                History::create(
                    new HistoryDto(
                        $helpOffer->sender_id,
                        $helpOffer->receiver_id,
                        $helpOffer->id,
                        HistoryEntity::TYPE_OFFER,
                        HistoryEntity::BALANCE_ADD,
                        0,
                        $help->amount
                    )
                );

                Transaction::create(
                    $helpOffer->sender_id,
                    $helpOffer->receiver_id,
                    0,
                    TransactionEntity::TYPE_OFFER,
                    $help->amount
                );

                //Update Completed offer number
                $help->offer_completed = $help->offer_completed + 1;
                $help->save();

                //If complete + (offerCompleted = Capacity)
                $num_of_offer_completed = $help->countOfferCompleted();
                if ($num_of_offer_completed >= $help->capacity) {
                    $help->is_completed = 1;
                    $help->save();

                    $feed_timeline = Timeline::where('reference_id', $help->id)->first();
                    if ($feed_timeline) {
                        $feed_timeline->status = 'inactive';
                        $feed_timeline->save();
                    }
                }
                break;
            default:
                Log::debug("Invalid status: " . $request->get('status'));
                throw new InCorrectFormatException('Invalid status');
                break;
        }

        return fractal($helpOffer, new HelpOfferTransform());
    }

    public function getOfferHistory(string $status = null)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $this->checkStatusFormat($status);
        $status = $status != null ? HelpOffer::$status[$status] : null;
        $offers = $this->helpOfferRepository->getOfferByUser($this->getUserId(), $perPage, $status);
        return fractal($offers, new HelpOfferTransform());
    }

    public function getOfferedHistory(string $status = null)
    {
        $perPage = app('request')->input('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $this->checkStatusFormat($status);
        $status = $status != null ? HelpOffer::$status[$status] : null;
        $offers = $this->helpOfferRepository->getOfferForUser($this->getUserId(), $perPage, $status);
        return fractal($offers, new HelpOfferTransform());
    }

    /**
     * @return int
     */
    private function getUserId()
    {
        return app('request')->user()->id;
    }

    private function checkStatusFormat($status)
    {
        if ($status) {
            if (!array_key_exists($status, HelpOffer::$status)) {
                throw new InCorrectFormatException('Status does not exist');
            }
        }
    }
}
