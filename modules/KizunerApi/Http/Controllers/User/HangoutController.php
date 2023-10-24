<?php

namespace Modules\KizunerApi\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Modules\KizunerApi\Exceptions\InCorrectFormatException;
use Modules\KizunerApi\Services\User\HistoryManager;
use Symfony\Component\HttpFoundation\Response;

class HangoutController
{
    public function getGuestHangoutHistory(HistoryManager $historyManager, $status = null)
    {
        try {
            return new JsonResponse($historyManager->getHangoutHistory($status), Response::HTTP_OK);
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

    public function getCastHangoutHistory(HistoryManager $historyManager, $status = null)
    {
        try {
            return new JsonResponse($historyManager->getOfferHistory($status), Response::HTTP_OK);
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
}
