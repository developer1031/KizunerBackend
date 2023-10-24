<?php

namespace Modules\KizunerApi\Http\Controllers\Hangout;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Feed\Models\Timeline;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\KizunerApi\Http\Requests\Hangout\OfferRequest;
use Modules\KizunerApi\Http\Requests\Hangout\OfferStatusChangeRequest;
use Modules\KizunerApi\Services\Hangout\OfferManager;
use Symfony\Component\HttpFoundation\Response;

class OfferController
{
    /**
     * @param OfferManager $offerManager
     * @param OfferRequest $request
     * @return JsonResponse
     */
    public function offerHangout(OfferManager $offerManager, OfferRequest $request)
    {
        if ($request->validated()) {
            try {
                $response = $offerManager->offerHangout($request);
                return new JsonResponse($response, Response::HTTP_CREATED);
            } catch (ModelNotFoundException | PermissionDeniedException $exception) {
                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * @param OfferManager $offerManager
     * @param OfferStatusChangeRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function changeOfferStatus(OfferManager $offerManager, OfferStatusChangeRequest $request, string $id)
    {
        if ($request->validated()) {
            try {

                if ($request->get('status') == 'approved') {
                    $offer = Offer::find($id);

                    if (($offer->status != Offer::$status['completed'] && $offer->status != Offer::$status['reject']) || $offer->payment_status != Offer::PAYMENT_STATUS_PAID) {
                        throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                    }

                    $hangout = $offer->hangout;
                    if ($hangout->is_min_capacity && intval($hangout->is_min_capacity) > 0) {
                        $offers_number = $hangout->offers()->whereIn('status', [3, 5, 9])->count();
                        if ($offers_number < intval($hangout->is_min_capacity)) {

                            return new JsonResponse([
                                'data' => [
                                    'errors' => [
                                        'message' => 'The offers is not reach to minimum. The minimum is: ' . $hangout->is_min_capacity
                                    ]
                                ]
                            ], 200);
                        }
                    }

                    $hangout->is_completed = 1;
                    $hangout->save();

                    $feed_timeline = Timeline::where('reference_id', $hangout->id)->first();
                    if ($feed_timeline) {
                        $feed_timeline->status = 'inactive';
                        $feed_timeline->save();
                    }
                }

                $response = $offerManager->changeStatus($request, $id);
                return new JsonResponse($response, Response::HTTP_OK);
            } catch (InCorrectFormatException $exception) {
                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function getOffers(OfferManager $offerManager, Request $request, string $id)
    {
        try {
            $response = $offerManager->getOffers($id);
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (PermissionDeniedException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors' => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode()
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
