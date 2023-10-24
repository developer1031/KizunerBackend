<?php

namespace Modules\Auth\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Modules\Auth\Contracts\AuthManagerInterface;
use Modules\Auth\Http\Requests\RefreshTokenRequest;

class RefreshTokenController
{
    private $authManager;

    public function __construct(AuthManagerInterface $authManager)
    {
        $this->authManager = $authManager;
    }

    public function action(RefreshTokenRequest $request)
    {
        if ($request->validated()) {
            try {
                return response()->json(
                    $this->authManager->createRefreshToken($request),
                    Response::HTTP_OK
                );
            } catch (ClientException $exception) {
                return response()->json([
                    'errors' => [
                        'message' => 'Invalid refresh token'
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
