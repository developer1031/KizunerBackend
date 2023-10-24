<?php

namespace Modules\KizunerApi\Services;

use Modules\Kizuner\Contracts\RattingRepositoryInterface;
use Modules\Kizuner\Exceptions\PermissionDeniedException;
use Modules\KizunerApi\Http\Requests\Ratting\CreateRattingRequest;
use Modules\KizunerApi\Http\Requests\Ratting\UpdateRattingRequest;
use Modules\KizunerApi\Transformers\RattingTransform;
use Modules\User\Contracts\UserRepositoryInterface;

class RattingManager
{

    private $rattingRepository;

    private $userRepository;

    public function __construct(
        RattingRepositoryInterface $rattingRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->rattingRepository = $rattingRepository;
    }

    public function addRatting(CreateRattingRequest $request)
    {
        $currentUser    =       $request->user()->id;
        $data           =       $request->all(['rate', 'comment', 'user_id']);

        if ($this->rattingRepository->isRatted($currentUser, $data['user_id'])) {
            throw new PermissionDeniedException('You already rated this user, you can edit or delete it');
        }

        if ($currentUser == $data['user_id']) {
            throw new PermissionDeniedException('You can\'t not rate yourself');
        }

        $user = $this->userRepository->get($data['user_id']);

        $data['user_id'] = $currentUser;
        $ratting = $this->rattingRepository->create($data);

        $user->rattings()->save($ratting);

        return fractal($ratting, new RattingTransform());
    }

    public function updateRatting(string $id, UpdateRattingRequest $request)
    {
        $ratting = $this->rattingRepository->update($id, [
            'comment' => $request->get('comment'),
            'rate'    => $request->get('rate')
        ]);
        return fractal($ratting, new RattingTransform());
    }

    public function deleteRatting(string $id)
    {
        $check = $this->rattingRepository->delete($id);
        return [
            'data' => [
                'status' => true,
                'message' => 'Delete Ratting Successful'
            ]
        ];
    }
}
