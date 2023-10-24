<?php

namespace Modules\Feed\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Modules\Feed\Contracts\Data\FeedFollowerInterfaceFactory;
use Modules\Feed\Contracts\Repositories\FeedFollowerRepositoryInterface;
use Modules\Feed\Models\FeedFollower;
use Modules\Feed\Models\FeedFollowerFactory;

class FeedFollowerRepository implements FeedFollowerRepositoryInterface
{

    /** @var FeedFollowerFactory $feedFollower */
    private $feedFollower;

    /**
     * FeedFollowerRepository constructor.
     * @param FeedFollowerInterfaceFactory $feedFollowerFactory
     */
    public function __construct(FeedFollowerInterfaceFactory $feedFollowerFactory)
    {
        $this->feedFollower = $feedFollowerFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(string $userId, string $channelId, string $status = 'active', string $scope = 'default'): FeedFollower
    {
        /** @var FeedFollower $feedFollower */
        $feedFlManager = $this->feedFollower->create();
        $feedFollower  = $feedFlManager->firstOrCreate([
            'user_id'       => $userId,
            'channel_id'    => $channelId,
            'scope'         => $scope,
            'status'        => $status
        ]);

        return $feedFollower;
    }


    /**
     * @inheritDoc
     */
    public function updateStatus(string $userId, string $channelId, string $status): FeedFollower
    {
        $feedFollower = $this->findByUserIdAndChannelId($userId, $channelId);
        $feedFollower->setStatus($status);

        $feedFollower->save();
        return $feedFollower;
    }

    /**
     * @inheritDoc
     */
    public function getFollowingList(string $userId): Collection
    {
        /** @var FeedFollower $fdManager */
        $fdManager = $this->feedFollower->create();

        return $fdManager->select('channel_id')
                                ->where('user_id', $userId)
                                ->where('status', '<>', 'inactive')
                                ->get();
    }


    /**
     * @inheritDoc
     */
    public function findByUserIdAndChannelId(string $userId, string $channelId): FeedFollower
    {
        $feedManager = $this->feedFollower->create();
        $feedFollower = $feedManager->where([
            'user_id'       => $userId,
            'channel_id'    => $channelId
        ])->first();

        if (!$feedFollower) {
            throw new ModelNotFoundException(
                'Feed Follower not found'
            );
        }
        return $feedFollower;
    }
}
