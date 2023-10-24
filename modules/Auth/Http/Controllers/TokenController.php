<?php

namespace Modules\Auth\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Response;
use Modules\Auth\Contracts\AuthManagerInterface;
use Modules\Auth\Http\Requests\TokenRequest;

class TokenController
{

    private $authManager;

    public function __construct(AuthManagerInterface $authManager)
    {
        $this->authManager = $authManager;
    }

    public function action(TokenRequest $request)
    {
        if ($request->validated()) {
            try {
                return response()->json(
                    $this->authManager->createCredentials($request),
                    Response::HTTP_OK
                );
            } catch (ClientException $exception) {
                return response()->json([
                    'errors' => [
                        'message' => 'Invalid email/password combination'
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
