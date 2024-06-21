<?php

namespace Modules\KizunerApi\Services\User\Relations;

use Modules\Friend\Events\FollowerCreatedEvent;
use Modules\Kizuner\Contracts\RelationshipRepositoryInterface;
use Modules\KizunerApi\Transformers\RelationTransform;
use Modules\KizunerApi\Transformers\FollowerTransform;
use Modules\Notification\Job\NewFollowJob;
use Modules\User\Exceptions\MissingInfoException;

class FollowManager
{

  private $relationRepository;

  public function __construct(RelationshipRepositoryInterface $repository)
  {
    $this->relationRepository = $repository;
  }

  public function followUser(string $followId)
  {
    $userId = app('request')->user()->id;

    $followObj = $this->relationRepository->follow($userId, $followId);
    event(new FollowerCreatedEvent($followObj));
    NewFollowJob::dispatch($followObj);

    return [
      'data' => [
        'id'        => $followObj->id,
        'status'    => true,
        'message'   => 'Follow successful!'
      ]
    ];
  }

  public function unFollowUser(string $id)
  {
    $followObj = $this->relationRepository->unFollow($id);

    return [
      'data' => [
        'status'    => true,
        'message'   => 'Unfollow successful!'
      ]
    ];
  }

  public function getFollows(string $userId)
  {
    $type = app('request')->input('type');
    $perPage = app('request')->input('per_page');

    if (!$perPage) {
      $perPage = 3;
    }

    if (!$type) {
      throw new MissingInfoException('Please add "?type=follower|following" to URL');
    }

    if ($type == 'follower') {
      $data = $this->relationRepository->getFollowers($userId, $perPage);
      if ($data) {
        return fractal($data, new RelationTransform());
      }
    } elseif ($type == 'following') {
      $data = $this->relationRepository->getFollowed($userId, $perPage);
      if ($data) {
        return fractal($data, new FollowerTransform());
      }
    } else {
      throw new MissingInfoException('Type must be: "following" OR "follower"');
    }
  }
}
