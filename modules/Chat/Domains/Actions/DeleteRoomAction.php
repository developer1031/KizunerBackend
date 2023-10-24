<?php

namespace Modules\Chat\Domains\Actions;

use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Room;

class DeleteRoomAction
{

    private $roomId;

    public function __construct(string $roomId)
    {
        $this->roomId = $roomId;
    }

    public function execute()
    {
        $this->deleteRoom();
        $this->deleteRoomMembers();
    }

    private function deleteRoom()
    {
        return Room::delete($this->roomId);
    }

    private function deleteRoomMembers()
    {
        return Member::deleteByRoomId($this->roomId);
    }
}
