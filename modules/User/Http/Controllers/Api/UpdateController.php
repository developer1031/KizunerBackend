<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Upload\Exceptions\FileNotExistException;
use Modules\User\Exceptions\AuthException;
use Modules\User\Http\Requests\Api\Update\AuthUpdateRequest;
use Modules\User\Http\Requests\Api\Update\GeneralUpdateRequest;
use Modules\User\Http\Requests\Api\Update\IndentityUpdateRequest;
use Modules\User\Http\Requests\Api\Update\LocationUpdateRequest;
use Modules\User\Http\Requests\Api\Update\MediaUpdateRequest;
use Modules\User\Services\User\UpdateManager;
use Symfony\Component\HttpFoundation\Response;

class UpdateController
{
    public function updateGeneralInfo(UpdateManager $updateManager, GeneralUpdateRequest $request)
    {
        return new JsonResponse(
            $updateManager->updateGeneralInfo($request),
            Response::HTTP_OK
        );
    }

    public function updateIdentityInfo(UpdateManager $updateManager, IndentityUpdateRequest $request)
    {
        return new JsonResponse(
            $updateManager->updateIdentityInfo($request),
            Response::HTTP_OK
        );
    }

    public function removeMedia(UpdateManager $updateManager)
    {
        try {
            return new JsonResponse(
                $updateManager->removeMedia(),
                Response::HTTP_OK
            );
        } catch (FileNotExistException $exception) {
            return new JsonResponse([
                'data' => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode()
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateLocation(UpdateManager $updateManager, LocationUpdateRequest $request)
    {
        return new JsonResponse(
            $updateManager->updateLocation($request),
            Response::HTTP_OK
        );
    }

    public function updateAuthInfo(UpdateManager $updateManager, AuthUpdateRequest $request)
    {
        try {
            return new JsonResponse(
                $updateManager->updateAuthInfo($request),
                Response::HTTP_OK
            );
        } catch (AuthException $exception) {
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
