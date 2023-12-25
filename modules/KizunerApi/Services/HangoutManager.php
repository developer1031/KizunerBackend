<?php

namespace Modules\KizunerApi\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Modules\Hangout\Events\HangoutCreatedEvent;
use Modules\Hangout\Events\HangoutDeletedEvent;
use Modules\Kizuner\Contracts\HangoutRepositoryInterface;
use Modules\Kizuner\Contracts\LocationRepositoryInterface;
use Modules\Kizuner\Contracts\MediaRepositoryInterface;
use Modules\Kizuner\Contracts\SkillRepositoryInterface;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\LeaderBoard;
use Modules\Kizuner\Models\Media;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Events\AddedPointSocketEvent;
use Modules\KizunerApi\Exceptions\InvalidDataException;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutCreateRequest;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutUpdateAvailableStatusRequest;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutUpdateRequest;
use Modules\KizunerApi\Transformers\HangoutTransform;
use Modules\Notification\Job\Hangout\HangoutTagJob;
use Modules\Upload\Models\UploadTrash;
use Modules\Wallet\Domains\Wallet;

class HangoutManager
{

    /** @var HangoutRepositoryInterface */
    private $hangoutRepository;

    /** @var MediaRepositoryInterface */
    private $mediaRepository;

    /** @var LocationRepositoryInterface */
    private $locationRepository;

    /** @var SkillRepositoryInterface */
    private $skillRepository;

    /**
     * HangoutManager constructor.
     * @param HangoutRepositoryInterface $hangoutRepository
     * @param LocationRepositoryInterface $locationRepository
     * @param MediaRepositoryInterface $mediaRepository
     * @param SkillRepositoryInterface $skillRepository
     */
    public function __construct(
        HangoutRepositoryInterface $hangoutRepository,
        LocationRepositoryInterface $locationRepository,
        MediaRepositoryInterface $mediaRepository,
        SkillRepositoryInterface $skillRepository
    ) {
        $this->skillRepository = $skillRepository;
        $this->locationRepository = $locationRepository;
        $this->mediaRepository = $mediaRepository;
        $this->hangoutRepository = $hangoutRepository;
    }

    /**
     * @param HangoutCreateRequest $request
     * @return \Spatie\Fractal\Fractal
     */
    public function createNewHangout(HangoutCreateRequest $request): \Spatie\Fractal\Fractal
    {

        // $wallet = Wallet::findByUserId(auth()->user()->id);
        // if (
        //     ($request->get('payment_method') == Hangout::PAYMENT_METHOD_CREDIT ||
        //         $request->get('payment_method') == Hangout::PAYMENT_METHOD_BOTH) &&
        //     !$wallet->payouts_enabled
        // ) {
        //     throw new Exception('Stripe connect is required');
        // }

        $hangoutData = [];
        if ($request->get('type') == Hangout::$type['single']) {
            $hangoutData = $request->all([
                'type', 'title', 'description', 'kizuna', 'capacity', 'start', 'end', 'available_status', 'short_address', 'payment_method', 'amount'
            ]);
        } else {
            $hangoutData = $request->all([
                'type', 'title', 'description', 'kizuna', 'schedule', 'end', 'available_status', 'short_address', 'payment_method', 'amount'
            ]);
        }
        Log::debug($hangoutData);

        if (!$request->available_status) {
            //$hangoutData['available_status'] = 'online';
            $hangoutData['available_status'] = null;
        }

        if ($request->has('room_id')) {
            $hangoutData['room_id'] = $request->get('room_id');
        }
        $hangoutData['is_min_capacity'] = 0;
        if ($request->has('isMinCapacity') && $request->get('isMinCapacity') !== null) {
            $hangoutData['is_min_capacity'] = $request->get('isMinCapacity');
        }

        $hangoutData['user_id'] = app('request')->user()->id;

        $hangoutData['is_range_price'] = $request->get('is_range_price');
        $hangoutData['min_amount'] = $request->get('min_amount');
        $hangoutData['max_amount'] = $request->get('max_amount');
        $hangoutData['crypto_wallet_id'] = $request->get('crypto_wallet_id');
        
        $hangout = $this->hangoutRepository->create($hangoutData);

        Log::debug($request->available_status);
        /** Save Location */
        if (!in_array($request->available_status, ['online', 'combine'])) {
            $locationData = $request->all([
                'address',
                'lat',
                'lng',
                'short_address'
            ]);
            $location = $this->locationRepository->create($locationData);
            $hangout->location()->save($location);
        }

        //        if ($request->get('cover')) {
        //            /** Save Media */
        //            $media = $this->mediaRepository->update($request->get('cover'), [
        //                'type' => Media::$type['hangout']['cover']
        //            ]);
        //            $hangout->media()->save($media);
        //        }

        /** Save Media */
        Log::debug($request->cover);
        if ($request->get('cover')) {
            $cover = $request->get('cover');
            if (str_contains($cover, ';')) {
                $coverArr = explode(";", $cover);
                foreach ($coverArr as $coverArrItem) {
                    $media = $this->mediaRepository->update($coverArrItem, [
                        'type' => Media::$type['hangout']['cover']
                    ]);
                    $hangout->media()->save($media);
                }
            } else {
                $media = $this->mediaRepository->update($request->get('cover'), [
                    'type' => Media::$type['hangout']['cover']
                ]);
                $hangout->media()->save($media);
            }
        }

        $hangout->skills()->sync($request->get('skills') ?? []);
        $hangout->categories()->sync($request->get('categories') ?? []);

        // + Point to leaderBoard
        // addPoint();
        /*
        $leaderboard = LeaderBoard::where('user_id', app('request')->user()->id)->first();
        if($leaderboard) {
            $is_up = $leaderboard->update_point(1);
            event(new AddedPointSocketEvent($is_up));
        }
        */

        //Generate Fake user & Help
        /*
        if($request->available_status!='no_time') {
            try {
                generateFakeUserHelps($hangout, 3);
            }
            catch (\Exception $e) {}
        }
        */

        generateFakeUserHelps($hangout, 3, $request);
        event(new HangoutCreatedEvent($hangout));

        //friends
        $friends = $request->get('friends');
        if ($friends) {
            $hangout->friends = $friends;
            $hangout->save();
        }

        //Send notification
        HangoutTagJob::dispatch($hangout);

        return fractal($hangout, new HangoutTransform());
    }

    /**
     * @param string $id
     * @param HangoutUpdateRequest $request
     * @return \Spatie\Fractal\Fractal
     * @throws InvalidDataException
     */
    public function updateHangout(string $id, HangoutUpdateRequest $request)
    {

        $hangout = $this->hangoutRepository->get($id);

        if (
            $request->get('capacity') &&
            $request->get('capacity') < ($hangout->capacity - $hangout->available)
        ) {
            throw new InvalidDataException('Capacity should greater than or equal to ' .
                $hangout->capacity - $hangout->available);
        } elseif (
            $request->get('capacity')
            && $request->get('capacity') >= ($hangout->capacity - $hangout->available)
        ) {
            $hangout->available = $request->get('capacity') - ($hangout->capacity - $hangout->available);
            $hangout->capacity = $request->get('capacity');
            $hangout->save();
        }

        if ($request->get('type') == Hangout::$type['single']) {
            $hangoutData = $request->all([
                'title', 'description', 'kizuna', 'capacity', 'start', 'end', 'available_status', 'short_address'
            ]);
        } else {
            $hangoutData = $request->all([
                'title', 'description', 'kizuna', 'schedule', 'available_status', 'short_address'
            ]);
        }
        if ($request->has('isMinCapacity') && $request->get('isMinCapacity') !== null) {
            $hangoutData['is_min_capacity'] = $request->get('isMinCapacity');
        }

        $hangoutData['user_id'] = app('request')->user()->id;
        $hangout = $this->hangoutRepository->update($id, $hangoutData);
        $hangout->skills()->sync($request->get('skills'));

        if ($hangout->location) {
            if ($hangout->location->address != $request->get('address')) {
                $hangout->location()->delete();
                $locationData = $request->all([
                    'address',
                    'lat',
                    'lng',
                    'short_address'
                ]);
                $location = $this->locationRepository->create($locationData);
                $hangout->location()->save($location);
            }
        } else {
            $locationData = $request->all([
                'address',
                'lat',
                'lng'
            ]);
            $location = $this->locationRepository->create($locationData);
            $hangout->location()->save($location);
        }

        if ($request->has('cover')) {
            if ($request->get('cover') == null) {
                if ($hangout->media != null) {
                    $hangoutMedias = $hangout->media;
                    foreach ($hangoutMedias as $hangoutMedia) {
                        UploadTrash::create([
                            ['path' => $hangoutMedia->path],
                            ['path' => $hangoutMedia->thumb]
                        ]);
                        $hangoutMedia->delete();
                    }
                }
            } else {
                $cover = $request->get('cover');
                $hangoutMedias = $hangout->media;

                if (str_contains($cover, ';')) {
                    $coverArr = explode(";", $cover);

                    if ($hangoutMedias != []) {
                        foreach ($hangoutMedias as $hangoutMedia) {
                            if (!in_array($hangoutMedia->id, $coverArr)) {
                                UploadTrash::create([
                                    ['path' => $hangoutMedia->path],
                                    ['path' => $hangoutMedia->thumb]
                                ]);
                                $hangoutMedia->delete();
                            }
                        }

                        foreach ($coverArr as $coverArrItem) {
                            $isExist = $hangoutMedias->first(function ($hangoutMedia) use ($coverArrItem) {
                                return $hangoutMedia->id == $coverArrItem;
                            });

                            if ($isExist == null) {
                                $media = $this->mediaRepository->update($coverArrItem, [
                                    'type' => Media::$type['hangout']['cover']
                                ]);
                                $hangout->media()->save($media);
                            }
                        }
                    } else {
                        foreach ($coverArr as $coverArrItem) {
                            $media = $this->mediaRepository->update($coverArrItem, [
                                'type' => Media::$type['hangout']['cover']
                            ]);
                            $hangout->media()->save($media);
                        }
                    }
                } else {
                    if ($hangoutMedias != []) {
                        foreach ($hangoutMedias as $hangoutMedia) {
                            if ($hangoutMedia->id != $cover) {
                                UploadTrash::create([
                                    ['path' => $hangoutMedia->path],
                                    ['path' => $hangoutMedia->thumb]
                                ]);
                                $hangoutMedia->delete();
                            }
                        }
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['hangout']['cover']
                        ]);
                        $hangout->media()->save($media);
                    } else {
                        $media = $this->mediaRepository->update($cover, [
                            'type' => Media::$type['hangout']['cover']
                        ]);
                        $hangout->media()->save($media);
                    }
                }
            }
        }
        $hangout->skills()->sync($request->get('skills'));
        $hangout->categories()->sync($request->get('categories'));

        //friends
        $friends = $request->get('friends');
        if ($friends) {
            $hangout->friends = $friends;
            $hangout->save();
        }

        $hangout = $this->hangoutRepository->get($id);
        return fractal($hangout, new HangoutTransform());
    }

    /**
     * @param int $userId
     * @return \Spatie\Fractal\Fractal
     */
    public function getHangoutByUser(string $userId)
    {
        $perPage = app('request')->get('per_page');

        if (!$perPage) {
            $perPage = 5;
        }

        $hangoutList = $this->hangoutRepository->getByUser($userId, $perPage);
        return fractal($hangoutList, new HangoutTransform());
    }

    /**
     * @param int $hangOutId
     * @return \Spatie\Fractal\Fractal
     */
    public function getHangoutDetail(string $hangOutId)
    {
        $hangout = $this->hangoutRepository->get($hangOutId);
        return fractal($hangout, new HangoutTransform(true));
    }

    /**
     * @param string $id
     * @return array
     */
    public function deleteHangout(string $id)
    {
        $currentUser = app('request')->user()->id;

        $hangout = $this->hangoutRepository->isHangoutOwner($currentUser, $id);

        if ($hangout) {

            $offerAccepted = $hangout->offers()->whereIn('status', [Offer::$status['accept']])->count();
            if ($offerAccepted >= 1) {
                return [
                    'data' => [
                        'message' => 'This Hangout has offer accepted, could not be deleted.',
                        'status'  => true
                    ]
                ];
            }

            $hangout->delete();
            event(new HangoutDeletedEvent($id));

            return [
                'data' => [
                    'message' => 'Delete Hangout Successful',
                    'status'  => true
                ]
            ];
        }
    }

    public function updateStatusHangout(string $id, HangoutUpdateAvailableStatusRequest $request)
    {
        $hangout = $this->hangoutRepository->get($id);
        $hangout->available_status = $request->get('available_status');
        $hangout->save();
        return fractal($hangout, new HangoutTransform());
    }
}
