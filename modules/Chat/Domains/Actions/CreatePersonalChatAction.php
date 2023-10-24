<?php

namespace Modules\Chat\Domains\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Room;
use Modules\User\Domains\User;

class CreatePersonalChatAction
{

    private $firstUser;

    private $secondUser;

    public function __construct(string $firstUserId, string $secondUserId)
    {
        $this->firstUser    = $firstUserId;
        $this->secondUser   = $secondUserId;
    }

    public function execute()
    {
        $roomIds = DB::table('chat_members as m')
                        ->select('r.id as id')
                        ->join('chat_rooms as r', 'r.id', '=', 'm.room_id')
                        ->where('m.user_id', $this->firstUser)
                        ->where('r.type', 'personal')
                        ->pluck('id')
                        ->toArray();

        $roomExist = DB::table('chat_members')
                        ->whereIn('room_id', $roomIds)
                        ->where('user_id', $this->secondUser)
                        ->first();

        if ($roomExist) {
            return Room::find($roomExist->room_id);
        }

        return $this->addMembers($this->createPersonalRoom());
    }

    private function createPersonalRoom()
    {

        $firstUser = User::find($this->firstUser);
        $secondUser = User::find($this->secondUser);
        $is_fake = 0;
        if( ($firstUser && $firstUser->is_fake) || ($secondUser && $secondUser->is_fake) ) {
            $is_fake = 1;
        }
        return Room::create(RoomEntity::TYPE_PERSONAL, $is_fake);
    }

    private function addMembers($room)
    {
        Member::create($room->id, $this->firstUser);
        Member::create($room->id, $this->secondUser);
        return $room;
    }
}
