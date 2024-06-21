<?php

namespace Modules\Kizuner\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Kizuner\Contracts\RelationshipRepositoryInterface;
use Modules\Kizuner\Models\User\Block;
use Modules\Kizuner\Models\User\Follow;
use Modules\Kizuner\Models\User\Friend;

class RelationRepository implements RelationshipRepositoryInterface
{

  /**
   * @inheritDoc
   */
  public function follow(string $userId, string $followId)
  {
    return Follow::updateOrCreate([
      'user_id'   => $userId,
      'follow_id' => $followId
    ]);
  }

  /**
   * @inheritDoc
   */
  public function unFollow(string $followId)
  {
    return Follow::find($followId)
      ->delete();
  }

  /**
   * @inheritDoc
   */
  public function getFollowers(string $userId, $perPage)
  {
    if ($userId == null) {
      $userId = app('request')->user()->id;
    }

    $searchTerm = app('request')->input('query');
    $sql = Follow::where('follow_id', $userId);
    if ($searchTerm) {
      $users = DB::table('users')
        ->whereNot('id', $userId)
        ->select('id')
        ->where('name', 'like', '%' . $searchTerm . '%')
        ->get()
        ->pluck('id')
        ->toArray();
      $sql->whereIn('user_id', $users);
    }
    return $sql->paginate($perPage);
  }

  /**
   * @inheritDoc
   */
  public function getFollowed(string $userId, $perPage)
  {
    if ($userId == null) {
      $userId = app('request')->user()->id;
    }

    $searchTerm = app('request')->input('query');

    if ($searchTerm) {
      $users = DB::table('users')
        ->whereNot('id', $userId)
        ->select('id')
        ->where('name', 'like', '%' . $searchTerm . '%')
        ->get()
        ->pluck('id')
        ->toArray();

      return Follow::whereIn('follow_id', $users)->paginate($perPage);
    }

    return Follow::all()->paginate($perPage);
  }

  /**
   * @inheritDoc
   */
  public function updateFriendStatus(string $friendRQID, int $status)
  {
    $fq = Friend::find($friendRQID);
    $fq->status = $status;
    $fq->save();
    return $fq;
  }

  /**
   * @inheritDoc
   */
  public function addFriendRequest(string $userId, string $friendId)
  {
    $friend =  new Friend([
      'user_id' => $userId,
      'friend_id' => $friendId,
      'status'   => Friend::$status['pending']
    ]);
    $friend->save();
    return $friend;
  }

  /**
   * @inheritDoc
   */
  public function blockUser(string $userId, string $blockId)
  {
    $block = new Block([
      'user_id' => $userId,
      'block_id' => $blockId
    ]);
    $block->save();
    return $block;
  }

  /**
   * @inheritDoc
   */
  public function unBlockUser(string $blockId)
  {
    $block = Block::find($blockId);
    return $block->delete();
  }

  /**
   * @inheritDoc
   */
  public function getBlockedUser(string $userId, $perPage)
  {
    $sql = Block::where('user_id', $userId);

    $searchTerm = app('request')->input('query');
    if ($searchTerm) {
      $users = DB::table('users')
        ->select('id')
        ->where('name', 'like', '%' . $searchTerm . '%')
        ->get()
        ->pluck('id')
        ->toArray();
      $sql->whereIn('block_id', $users);
    }

    return $sql->paginate($perPage);
  }

  /**
   * @inheritDoc
   */
  public function getFriends(string $userId, $perPage, string $status = null)
  {
    if ($status) {
      $status = Friend::$status[$status];
    } else {
      $status = Friend::$status['accept'];
    }

    $searchTerm = app('request')->input('query');

    $users = null;
    if ($searchTerm) {
      $users = DB::table('users')
        ->select('id')
        ->where('name', 'like', '%' . $searchTerm . '%')
        ->whereNull('deleted')
        ->get()
        ->pluck('id')
        ->toArray();
    }

    $sql = Friend::where(function ($q) use ($userId, $status) {
      $q->where('friend_id', $userId);
      if ($status != Friend::$status['pending']) {
        $q->orWhere('user_id', $userId);
      }
    });
    $sql->where('status', $status);

    if ($users !== null) {
      $sql->where(function ($query) use ($users) {
        $query->whereIn('friend_id', $users);
        $query->orWhereIn('user_id', $users);
      });
    }
    return $sql->paginate($perPage);
  }

  /**
   * @inheritDoc
   */
  public function deleteFriend(string $id)
  {
    $friendShip = Friend::where('id', $id)->firstOrFail();
    return $friendShip->delete();
  }

  /**
   * @param string $userId
   * @param string $friendId
   * @return mixed
   */
  public function isFriendShipExist(string $userId, string $friendId)
  {
    return Friend::where(function ($query) use ($userId, $friendId) {
      $query->where('user_id', $userId);
      $query->where('friend_id', $friendId);
      $query->where('status', 2);
    })->orWhere(function ($query) use ($userId, $friendId) {
      $query->where('friend_id', $userId);
      $query->where('user_id', $friendId);
      $query->where('status', 2);
    })
      ->first();
  }

  /**
   * @param string $userId
   * @param string $blockId
   * @return mixed
   */
  public function checkBlock(string $userId, string $blockId)
  {
    return Block::where(function ($query) use ($userId, $blockId) {
      $query->where('user_id', $userId);
      $query->where('block_id', $blockId);
    })->orWhere(function ($query) use ($userId, $blockId) {
      $query->where('block_id', $userId);
      $query->where('user_id', $blockId);
    })->first();
  }

  /**
   * @param string $userId
   * @param string $followId
   * @return mixed|void
   */
  public function removeFollow(string $userId, string $followId)
  {
    $relations = Follow::where(function ($query) use ($userId, $followId) {
      $query->where('user_id', $userId);
      $query->where('follow_id', $followId);
    })->orWhere(function ($query) use ($userId, $followId) {
      $query->where('follow_id', $userId);
      $query->where('user_id', $followId);
    })->get();
    if ($relations->count() > 0) {
      $relations->each(function ($item) {
        $item->delete();
      });
    }
  }

  public function removeFriend(string $userId, string $relationId)
  {
    $relations = Friend::where(function ($query) use ($userId, $relationId) {
      $query->where('user_id', $userId);
      $query->where('friend_id', $relationId);
    })->orWhere(function ($query) use ($userId, $relationId) {
      $query->where('friend_id', $userId);
      $query->where('user_id', $relationId);
    })->get();

    if ($relations->count() > 0) {
      $relations->each(function ($item) {
        $item->delete();
      });
    }
  }
}
