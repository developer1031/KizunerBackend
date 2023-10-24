<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Auth\Contracts\AuthManagerInterface;
use Modules\Auth\Http\Requests\SocialTokenRequest;

class SocialTokenController
{
    private $authManager;

    public function __construct(AuthManagerInterface $authManager)
    {
        $this->authManager = $authManager;
    }

    public function action(SocialTokenRequest $request)
    {
        if ($request->validated()) {
            try {
                return response()->json(
                    $this->authManager->createSocialToken($request),
                    Response::HTTP_OK
                );
            } catch (\Exception $exception) {
                return response()->json([
                    'errors' => [
                        'message' => 'Access token invalid'
                    ]
                ], Response::HTTP_UNAUTHORIZED);
            }
        }
    }
}
