<?php

namespace Modules\Chat\Domains\Repositories\Contracts;

interface MessageRepositoryInterface
{
   public function getByRoomId(string $roomId, $perPage);
}
