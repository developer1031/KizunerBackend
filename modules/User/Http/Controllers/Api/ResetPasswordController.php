<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\User\Exceptions\WrongPinException;
use Modules\User\Http\Requests\Api\User\ChangePasswordRequest;
use Modules\User\Http\Requests\Api\User\PinVerifyRequest;
use Modules\User\Http\Requests\Api\User\ResetPasswordRequest;
use Modules\User\Services\User\ResetPasswordManager;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController
{
    public function requestReset(ResetPasswordRequest $request, ResetPasswordManager $passwordManager)
    {
        if ($request->validated()) {
            try {
                $email = $request->get('email');
                return new JsonResponse(
                    $passwordManager->sendResetLink($email),
                    Response::HTTP_CREATED
                );
            } catch (PermissionDeniedException $exception) {
                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'errors'  => [
                        'message' => "Email does not exists. Please check again",
                        'code'   =>  $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function pinVerify(ResetPasswordManager $resetPasswordManager, PinVerifyRequest $request)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $resetPasswordManager->verifyPin($request),
                    Response::HTTP_OK
                );
            } catch (WrongPinException $exception) {
                return new JsonResponse([
                    'message' => $exception->getMessage(),
                    'errors'  => [
                        'message' => $exception->getMessage(),
                        'code'    => $exception->getCode()
                    ]
                ], Response::HTTP_BAD_REQUEST);
            }
        }
    }

    public function resetPassword(ResetPasswordManager $resetPasswordManager, ChangePasswordRequest $request)
    {
        try {
            return new JsonResponse(
                $resetPasswordManager->changePassword($request),
                Response::HTTP_OK
            );
        } catch (\Exception $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors'  => [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode()
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
