<?php

namespace Modules\KizunerApi\Http\Controllers\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Http\Requests\User\Relations\AddFriendRequest;
use Modules\KizunerApi\Services\User\Relations\FriendShipManager;
use Symfony\Component\HttpFoundation\Response;

class FriendShipController
{
    public function addFriendRequest(FriendShipManager $friendShipManager, AddFriendRequest $request)
    {
        if ($request->validated()) {
            try {
                return new JsonResponse(
                    $friendShipManager->addFriend($request->get('user_id')),
                    Response::HTTP_CREATED
                );
            } catch (PermissionDeniedException $exception) {
                return new JsonResponse([
                    'message' => 'Add Friend Unsuccessful',
                    'errors' => [
                        'message' => 'Add Friend Unsuccessful',
                        'code'    => $exception->getCode()
                    ]
                ], Response::HTTP_FORBIDDEN);
            }
        }
    }

    public function friendRequestReact(FriendShipManager $friendShipManager, string $id)
    {
        return new JsonResponse(
            $friendShipManager->updateFriendRequest($id),
            Response::HTTP_OK
        );
    }

    public function getFriendLists(FriendShipManager $friendShipManager, string $id = null)
    {

        if (!$id) {
            $id = app('request')->user()->id;
        }

        return new JsonResponse(
            $friendShipManager->getFriends($id),
            Response::HTTP_OK
        );
    }

    public function unFriend(FriendShipManager $friendShipManager, string $id)
    {
        try {
            return new JsonResponse(
                $friendShipManager->unFriend($id),
                Response::HTTP_OK
            );
        } catch (ModelNotFoundException $exception) {
            return new JsonResponse([
                'message' => 'This friendship not exist',
                'errors'  => [
                    'status' => true,
                    'message' => 'This friendship not exist'
                ]
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
