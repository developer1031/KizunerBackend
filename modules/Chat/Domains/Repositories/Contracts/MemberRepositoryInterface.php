<?php

namespace Modules\Chat\Domains\Repositories\Contracts;

interface MemberRepositoryInterface
{
    public function getByRoomId(string $roomId);

    public function getByUserId(string $userId, $perPage);
}
