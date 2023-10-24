<?php

namespace Modules\Feed\Contracts\Repositories;

use Illuminate\Support\Collection;
use Modules\Feed\Models\FeedFollower;

interface FeedFollowerRepositoryInterface
{
    /**
     * @param string $userId
     * @param string $channelId
     * @param string $status
     * @param string $scope
     * @return FeedFollower
     */
    public function create(string $userId, string $channelId, string $status = 'active', string $scope = 'default'): FeedFollower;

    /**
     * @param string $userId
     * @param string $channelId
     * @param string $status
     * @return FeedFollower
     */
    public function updateStatus(string $userId, string $channelId, string $status): FeedFollower;

    /**
     * @param string $userId
     * @return Collection
     */
    public function getFollowingList(string $userId): Collection;
}
