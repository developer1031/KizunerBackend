<?php

namespace Modules\KizunerApi\Services\User\Relations;

use Modules\Friend\Events\BlockCreatedEvent;
use Modules\Kizuner\Contracts\RelationshipRepositoryInterface;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Transformers\RelationTransform;

class BlockManager
{

    private $relationRepository;

    public function __construct(RelationshipRepositoryInterface $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    public function blockUser($userId)
    {

        $currentUser = app('request')->user()->id;

        if ($this->relationRepository->checkBlock($userId, $currentUser)) {
            throw new PermissionDeniedException('You already block this user');
        }

        $block = $this->relationRepository->blockUser($currentUser, $userId);

        $this->relationRepository->removeFollow($currentUser, $userId);

        $this->relationRepository->removeFriend($currentUser, $userId);

        event(new BlockCreatedEvent($block));

        return [
            'data' => [
                'status' => true,
                'message' => 'Block User Successful'
            ]
        ];
    }

    public function getBlockList()
    {
        $currentUser = app('request')->user()->id;
        $perPage     = app('request')->input('per_page');

        $query   = app('request')->input('query');

        if (!$perPage) {
            $perPage = 5;
        }

        $blockList = $this->relationRepository->getBlockedUser($currentUser, $perPage);

        return fractal($blockList, new RelationTransform());
    }

    public function unBlock($blockId)
    {
        $check = $this->relationRepository->unBlockUser($blockId);

        if ($check) {
            return [
                'data' => [
                    'message' => 'Unblock Successful',
                    'status'  => true
                ]
            ];
        }
    }
}
