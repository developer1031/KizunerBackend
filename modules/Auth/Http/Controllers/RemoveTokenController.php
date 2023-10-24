<?php

namespace Modules\Auth\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Auth\Contracts\AuthManagerInterface;

class RemoveTokenController
{

    /** @var AuthManagerInterface  */
    private $authManager;

    /**
     * LogoutController constructor.
     * @param AuthManagerInterface $authManager
     */
    public function __construct(AuthManagerInterface $authManager)
    {
        $this->authManager = $authManager;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function action()
    {
        try {
            return response()
                ->json(
                    $this->authManager->removeUserToken(),
                    Response::HTTP_OK
                );
        } catch (\Exception $exception) {
            return response()->json([
                'errors' => [
                    'message' => $exception->getMessage()
                ]
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
