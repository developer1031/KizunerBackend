<?php

namespace Modules\KizunerApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Modules\KizunerApi\Http\Requests\Status\StatusCreateRequest;
use Modules\KizunerApi\Http\Requests\Status\StatusUpdateRequest;
use Modules\KizunerApi\Services\StatusManager;
use Symfony\Component\HttpFoundation\Response;

class StatusController
{
    public function addStatus(StatusManager $statusManager, StatusCreateRequest $request)
    {
        return new JsonResponse(
            $statusManager->addStatus($request),
            Response::HTTP_CREATED
        );
    }

    public function updateStatus(StatusManager $statusManager, string $id, StatusUpdateRequest $request)
    {
        return new JsonResponse(
            $statusManager->updateStatus($id, $request),
            Response::HTTP_OK
        );
    }

    public function removeStatus(StatusManager $statusManager, string $id)
    {
        return new JsonResponse(
            $statusManager->removeStatus($id),
            Response::HTTP_OK
        );
    }

    public function getStatus(StatusManager $statusManager, string $id)
    {
        return new JsonResponse(
            $statusManager->getStatus($id),
            Response::HTTP_OK
        );
    }
}
