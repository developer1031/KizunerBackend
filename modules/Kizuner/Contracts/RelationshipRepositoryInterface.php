<?php

namespace Modules\Kizuner\Contracts;

use Modules\Kizuner\Models\User\Friend;

interface RelationshipRepositoryInterface
{
    /**
     * Follow a user
     * @param string $userId
     * @param string $followId
     * @return mixed
     */
    public function follow(string $userId, string $followId);

    /**
     * Unfollow a user
     * @param string $userId
     * @param string $followId
     * @return mixed
     */
    public function unFollow(string $followId);

    /**
     * @param string $userId
     * @param string $followId
     * @return mixed
     */
    public function removeFollow(string $userId, string $followId);

    /**
     * Get all followers
     * @param string $userId
     * @return mixed
     */
    public function getFollowers(string $userId, $perPage);

    /**
     * Get following user list
     * @param string $userId
     * @return mixed
     */
    public function getFollowed(string $userId, $perPage);

    /**
     * Send friend request
     * @param string $userId
     * @param string $friendId
     * @return Friend
     */
    public function addFriendRequest(string $userId, string $friendId);

    /**
     * @param string $friendRQID
     * @return mixed
     */
    public function updateFriendStatus(string $friendRQID, int $status);

    /**
     * @param string $userId
     * @param string $status
     * @param $query
     * @param int $perPage
     * @return mixed
     */
    public function getFriends(string $userId, $perPage, string $status);

    /**
     * Block user
     * @param string $userId
     * @param string $blockId
     * @return mixed
     */
    public function blockUser(string $userId, string $blockId);

    /**
     * unblock User
     * @param string $blockId
     * @return mixed
     */
    public function unBlockUser(string $blockId);

    /**
     * @param string $userId
     * @param int $perPage
     * @return mixed
     */
    public function getBlockedUser(string $userId, $perPage);

    /**
     * @param string $id
     * @return mixed
     */
    public function deleteFriend(string $id);

    /**
     * @param string $userId
     * @param string $friendId
     * @return mixed
     */
    public function isFriendShipExist(string $userId, string $friendId);

    /**
     * @param string $userId
     * @param string $blockId
     * @return mixed
     */
    public function checkBlock(string $userId, string $blockId);

    /**
     * @param string $userId
     * @param string $relationId
     * @return mixed
     */
    public function removeFriend(string $userId, string $relationId);
}
