<?php

namespace Modules\KizunerApi\Http\Controllers\User;

use Illuminate\Http\JsonResponse;
use Modules\KizunerApi\Http\Requests\User\Relations\FollowRequest;
use Modules\KizunerApi\Services\User\Relations\FollowManager;
use Modules\User\Exceptions\MissingInfoException;
use Psy\Util\Json;
use Symfony\Component\HttpFoundation\Response;

class FollowController
{
    public function followUser(FollowManager $followManager, FollowRequest $request)
    {
        if ($request->validated()) {
            return new JsonResponse(
                $followManager->followUser($request->get('user_id')),
                Response::HTTP_CREATED
            );
        }
    }

    public function unFollowUser(FollowManager $followManager, string $id)
    {

        return new JsonResponse(
            $followManager->unFollowUser($id),
            Response::HTTP_OK
        );
    }

    public function getFollows(FollowManager $followManager, string $id = null)
    {

        if (!$id) {
            $id = app('request')->user()->id;
        }

        try {
            return new JsonResponse(
                $followManager->getFollows($id),
                Response::HTTP_OK
            );
        } catch (MissingInfoException $exception) {
            return new JsonResponse([
                'message' => $exception->getMessage(),
                'errors' => [
                    'status' => false,
                    'message' => $exception->getMessage()
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
