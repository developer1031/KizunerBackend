<?php

namespace Modules\Helps\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\Feed\Models\Timeline;
use Modules\Helps\Http\Requests\HelpCancelRequest;
use Modules\Helps\Http\Requests\HelpCreateRequest;
use Modules\Helps\Http\Requests\HelpOfferRequest;
use Modules\Helps\Http\Requests\HelpOfferStatusChangeRequest;
use Modules\Helps\Http\Requests\HelpUpdateRequest;
use Modules\Helps\Http\Requests\HelpUpdateStatusRequest;
use Modules\Helps\Models\HelpOffer;
use Modules\Helps\Models\ShortLink;
use Modules\Helps\Services\HelpManager;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutUpdateAvailableStatusRequest;
use Modules\KizunerApi\Services\HangoutManager;
use Symfony\Component\HttpFoundation\Response;
use Str;

class HelpsController
{
    /*
     * @param HelpCreateRequest $request
     * @return JsonResponse
     */
    public function createNewHelp(HelpManager $helpManager, HelpCreateRequest $request)
    {
        Log::info("createNewHelp");
        Log::info($request->all());

        if ($request->validated()) {
            try {
                $response = $helpManager->createNewHelp($request);
                return new JsonResponse($response->toArray(), Response::HTTP_CREATED);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function getCurrentTime()
    {
        return new JsonResponse([
            'data'  => [
                "currentTime" => Carbon::now(),
            ]
        ], Response::HTTP_OK);
    }


    public function updateHelp(HelpManager $helpManager, HelpUpdateRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                $response = $helpManager->updateNewHelp($id, $request);
                return new JsonResponse($response->toArray(), Response::HTTP_CREATED);
            } catch (\Exception $exception) {
                return new JsonResponse([
                    'errors' => [
                        'message' => $exception->getMessage(),
                        'code' => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * @param HelpManager $helpManager
     * @param int|null $id
     * @return JsonResponse
     */
    public function getHangoutListByUser(HelpManager $helpManager, string $id = null)
    {
        if ($id == null) {
            $id = app('request')->user()->id;
        }
        $response = $helpManager->getHelpByUser($id);
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param HelpManager $helpManager
     * @param int $helpId
     * @return JsonResponse
     */
    public function getHelpDetail(HelpManager $helpManager, string $helpId)
    {
        return new JsonResponse(
            $helpManager->getHelpDetail($helpId),
            Response::HTTP_OK
        );
    }

    /**
     * @param HelpManager $helpManager
     * @param string $id
     * @return JsonResponse
     */
    public function delete(HelpManager $helpManager, string $id)
    {
        try {
            return new JsonResponse(
                $helpManager->deleteHelp($id),
                Response::HTTP_OK
            );
        } catch (ModelNotFoundException $exception) {
            return new JsonResponse([
                'errors' => [
                    'code' => $exception->getCode(),
                    'message' => 'You don\'t have permission to Delete this Hangout'
                ], Response::HTTP_FORBIDDEN
            ]);
        }
    }

    public function updateStatus(HelpManager $helpManager, HelpUpdateStatusRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $helpManager->updateStatus($id, $request),
                    Response::HTTP_OK
                );
            } catch (ModelNotFoundException $exception) {
                return new JsonResponse([
                    'errors' => [
                        'code' => $exception->getCode(),
                        'message' => 'You don\'t have permission to Update this Hangout'
                    ], Response::HTTP_FORBIDDEN
                ]);
            }
        }
    }

    public function requestCancel(HelpManager $helpManager, HelpCancelRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                $help =  $helpManager->requestCancel($id, $request);
                if ($help == null) {
                    return new JsonResponse([
                        'errors' => [
                            'message' => 'This help cannot be cancel because this have offer accepted'
                        ], Response::HTTP_FORBIDDEN
                    ]);
                }
                return new JsonResponse(
                    $help,
                    Response::HTTP_OK
                );
            } catch (ModelNotFoundException $exception) {
                return new JsonResponse([
                    'errors' => [
                        'code' => $exception->getCode(),
                        'message' => 'You don\'t have permission to cancel this help'
                    ], Response::HTTP_FORBIDDEN
                ]);
            }
        }
    }

    /*
     * Offer
     */
    //public function offer(HelpManager $helpManager, string $id) {
    public function offer(HelpManager $helpManager, HelpOfferRequest $request)
    {
        if ($request->validated()) {
            try {
                $response = $helpManager->offer($request);
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

    public function getOffers(HelpManager $helpManager, Request $request, string $id)
    {
        try {
            $response = $helpManager->getOffers($id);
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

    public function changeOfferStatus(HelpManager $helpManager, HelpOfferStatusChangeRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                if ($request->get('status') == 'approved') {
                    $offer = HelpOffer::find($id);

                    if (($offer->status != HelpOffer::$status['completed'] && $offer->status != HelpOffer::$status['reject']) || $offer->payment_status != HelpOffer::PAYMENT_STATUS_PAID) {
                        throw new InCorrectFormatException('Cannot change to ' . $request->get('status'));
                    }

                    $help = $offer->help;
                    if ($help->is_min_capacity && intval($help->is_min_capacity) > 0) {
                        $offers_number = $help->offer_accepted;
                        if ($offers_number < intval($help->is_min_capacity)) {

                            return new JsonResponse([
                                'data' => [
                                    'errors' => [
                                        'message' => 'The offers is not reach to minimum. The minimum is: ' . $help->is_min_capacity
                                    ]
                                ]
                            ], 200);
                        }
                    }
                }

                $response = $helpManager->changeStatus($request, $id);
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

    public function getCastHelpHistory(HelpManager $helpManager, $status = null)
    {
        try {
            return new JsonResponse($helpManager->getOfferHistory($status), Response::HTTP_OK);
        } catch (InCorrectFormatException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors'  => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode()
                ]
            ]);
        }
    }

    public function getGuestHelpHistory(HelpManager $helpManager, $status = null)
    {
        try {
            return new JsonResponse($helpManager->getOfferedHistory($status), Response::HTTP_OK);
        } catch (InCorrectFormatException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors'  => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode()
                ]
            ]);
        }
    }

    public function shortlinkStore(Request $request)
    {
        $request->validate([
            'link' => 'required|url'
        ]);

        $input['link'] = $request->link;
        $input['code'] = Str::random(10);

        $shortLink = ShortLink::create($input);

        return new JsonResponse(['success' => true, 'code' => $shortLink->code], Response::HTTP_OK);
    }

    public function shortenLink($code)
    {
        $find = ShortLink::where('code', $code)->first();
        return redirect($find->link);
    }
}
