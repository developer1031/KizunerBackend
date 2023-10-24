<?php

namespace Modules\Chat\Domains\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Message;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Chat\Domains\Room;
use Modules\Chat\Http\Transformers\MessageTransformer;

class RoomsQuery
{

    private $roomId;

    public function __construct(string $id)
    {
        $this->roomId = $id;
    }

    public function execute()
    {
        return $this->getChatRoom();
    }

    private function getChatRoom()
    {
         $chatRoom = DB::table('chat_rooms')
                        ->where('id', $this->roomId)
                        ->first();
         $chatRoom->users = $this->getRoomUsers($chatRoom->id);

         //Update Chat_room
        foreach ($chatRoom->users as $user) {
            if($user->is_fake) {
                $is_fake = 1;
                Room::update($this->roomId, '', $is_fake);
                break;
            }
        }
        return $chatRoom;
    }

    private function getRoomUsers(string $roomId)
    {
        return DB::table('chat_members')
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.online as online',
                    'users.is_fake',
                    'users.fake_avatar',
                    'uploads.thumb as user_avatar',
                    'chat_members.owner as owner',
                    'chat_members.seen_at as seen_at'
                )
                ->join('users','users.id', '=', 'chat_members.user_id')
                ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                ->where('chat_members.room_id', $roomId)
                ->groupBy('users.id')
                ->get();
    }
}
