<?php

namespace Modules\KizunerApi\Http\Controllers;

use Composer\Package\Package;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Kizuner\Models\Hangout;
use Modules\Kizuner\Models\Offer;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutCreateRequest;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutUpdateAvailableStatusRequest;
use Modules\KizunerApi\Http\Requests\Hangout\HangoutUpdateRequest;
use Modules\KizunerApi\Services\HangoutManager;
use Symfony\Component\HttpFoundation\Response;

class HangoutController
{
    /**
     * @param HangoutManager $hangoutManager
     * @param HangoutCreateRequest $request
     * @return JsonResponse
     */
    public function createNewHangout(HangoutManager $hangoutManager, HangoutCreateRequest $request)
    {
        Log::info('_-------------Create hangout -----');
        Log::info($request->all());
        if ($request->validated()) {
            try {
                $response = $hangoutManager->createNewHangout($request);
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
     * @param HangoutManager $hangoutManager
     * @param HangoutUpdateRequest $request
     * @param string $id
     * @return JsonResponse
     * @throws \Modules\KizunerApi\Exceptions\InvalidDataException
     */
    public function updateHangout(HangoutManager $hangoutManager, HangoutUpdateRequest $request, string $id)
    {
        Log::info('_-------------Create hangout -----');
        Log::info($request->all());
        if ($request->validated()) {
            try {
                $response = $hangoutManager->updateHangout($id, $request);
                return new JsonResponse($response, Response::HTTP_OK);
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
     * @param HangoutManager $hangoutManager
     * @param int|null $id
     * @return JsonResponse
     */
    public function getHangoutListByUser(HangoutManager $hangoutManager, string $id = null)
    {
        if ($id == null) {
            $id = app('request')->user()->id;
        }
        $response = $hangoutManager->getHangoutByUser($id);
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @param HangoutManager $hangoutManager
     * @param int $hangOutId
     * @return JsonResponse
     */
    public function getHangoutDetail(HangoutManager $hangoutManager, string $hangOutId)
    {
        return new JsonResponse(
            $hangoutManager->getHangoutDetail($hangOutId),
            Response::HTTP_OK
        );
    }

    /**
     * @param HangoutManager $hangoutManager
     * @param string $id
     * @return JsonResponse
     */
    public function delete(HangoutManager $hangoutManager, string $id)
    {
        try {
            $hangout = Hangout::find($id);
            return new JsonResponse(
                $hangoutManager->deleteHangout($id),
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

    public function updateStatus(HangoutManager $hangoutManager, HangoutUpdateAvailableStatusRequest $request, string $id)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $hangoutManager->updateStatusHangout($id, $request),
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
}
