<?php

namespace Modules\KizunerApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\KizunerApi\Http\Requests\React\HangoutReactRequest;
use Modules\KizunerApi\Http\Requests\React\HelpReactRequest;
use Modules\KizunerApi\Http\Requests\React\StatusReactRequest;
use Modules\KizunerApi\Services\ReactManager;
use Symfony\Component\HttpFoundation\Response;

class ReactController
{
    public function reactHangout(ReactManager $reactManager, HangoutReactRequest $request)
    {
        $response = $reactManager->reactHangout($request);

        if ($response['data']['status']) {
            return new JsonResponse(
                $response,
                Response::HTTP_OK
            );
        }
        return new JsonResponse([
            'message' => 'Hangout has been deleted',
            'errors'  => [
                'message' => 'Hangout has been deleted',
                'status'  => false
            ]
        ],Response::HTTP_NOT_FOUND);
    }

    public function reactStatus(ReactManager $reactManager, StatusReactRequest $request)
    {
        $response = $reactManager->reactStatus($request);

        if ($response['data']['status']) {
            return new JsonResponse(
                $response,
                Response::HTTP_OK
            );
        }
        return new JsonResponse([
            'message' => 'Status has been deleted',
            'errors'  => [
                'message' => 'Status has been deleted',
                'status'  => false
            ]
        ],Response::HTTP_NOT_FOUND);
    }

    public function reactHelp(ReactManager $reactManager, HelpReactRequest $request)
    {
        $response = $reactManager->reactHelp($request);

        if ($response['data']['status']) {
            return new JsonResponse(
                $response,
                Response::HTTP_OK
            );
        }
        return new JsonResponse([
            'message' => 'Help has been deleted',
            'errors'  => [
                'message' => 'Help has been deleted',
                'status'  => false
            ]
        ],Response::HTTP_NOT_FOUND);
    }
}
