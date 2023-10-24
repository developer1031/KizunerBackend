<?php

namespace Modules\KizunerApi\Services\User\Relations;

use Modules\Friend\Events\FriendAcceptedEvent;
use Modules\Friend\Events\FriendCreatedEvent;
use Modules\Kizuner\Contracts\RelationshipRepositoryInterface;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;
use Modules\KizunerApi\Transformers\RelationTransform;
use Modules\Notification\Job\FriendAcceptedJob;
use Modules\Notification\Job\FriendRequestJob;
use Modules\User\Exceptions\MissingInfoException;

class FriendShipManager
{
    private $relationRepository;

    public function __construct(RelationshipRepositoryInterface $relationshipRepository)
    {
        $this->relationRepository = $relationshipRepository;
    }

    public function addFriend(string $userId)
    {
        $friendShipCheck = $this->relationRepository->isFriendShipExist(
            app('request')->user()->id,
            $userId
        );

        $idBlocked = $this->relationRepository->checkBlock(
            app('request')->user()->id,
            $userId
        );

        if ($friendShipCheck || $idBlocked) {
            throw new PermissionDeniedException('You can not add this user as Friends');
        }

        $friendShip = $this->relationRepository->addFriendRequest(
            app('request')->user()->id,
            $userId
        );

        $this->relationRepository->follow(app('request')->user()->id, $userId);

        event(new FriendCreatedEvent($friendShip));

        //Send Noti
        FriendRequestJob::dispatch($friendShip);
        return [
            'data' => [
                'id' => $friendShip->id,
                'status' => TRUE,
                'message' => 'Successful added'
            ]
        ];
    }

    public function updateFriendRequest(string $friendShipId)
    {
        $action = app('request')->input('action');

        if (!$action) {
            throw new MissingInfoException('Please add "action=accept|reject" to your URL Param');
        }
        $status = Friend::$status[$action];
        $friendShip = $this->relationRepository->updateFriendStatus($friendShipId, $status);

        if ($action == 'accept') {
            event(new FriendAcceptedEvent($friendShip));
            FriendAcceptedJob::dispatch($friendShip);
            return [
                'data' => [
                    'id' => $friendShip->id,
                    'status' => TRUE,
                    'message' => 'Updated Successful'
                ]
            ];
        }

        if ($action === 'reject') {
            $friendShip->delete();
            return [
                'data' => [
                    'status' => TRUE,
                    'message' => 'Updated Successful'
                ]
            ];
        }

    }

    public function getFriends(string $id)
    {
        $perPage = app('request')->input('per_page');
        $status  = app('request')->input('status');
        if (!$perPage) {
            $perPage = 5;
        }

        $friendsList = $this->relationRepository->getFriends($id,$perPage, $status);
        return fractal($friendsList, new RelationTransform());
    }

    public function unFriend(string $id)
    {

        $friend = Friend::find($id);

        if ($friend) {
            $follow = Follow::where([
                'user_id' => $friend->user_id,
                'follow_id' => $friend->friend_id
            ])->first();

            if ($follow) {
                $follow->delete();
            }

            $followed = Follow::where([
                'user_id' => $friend->friend_id,
                'follow_id' => $friend->user_id
            ])->first();

            if ($followed) {
                $followed->delete();
            }
        }

        $this->relationRepository->deleteFriend($id);

        return [
            'data' => [
                'status' => true,
                'message' => 'Unfriend Successful'
            ]
        ];
    }
}
