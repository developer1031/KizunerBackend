<?php

namespace Modules\Chat\Domains\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Room;

class RoomMemberQuery
{


    private $room;

    public function __construct($room)
    {
        $this->room = $room;
    }

    public function execute()
    {
         $this->room->users = DB::table('chat_members')
                    ->select(
                        'users.id as user_id',
                        'users.name as user_name',
                        'uploads.thumb as user_avatar',
                        'chat_members.owner as owner',
                        'users.online as online',
                        'users.is_fake as is_fake',
                        'users.fake_avatar as fake_avatar',
                        'chat_members.seen_at as seen_at'
                        )
                    ->join('users', 'users.id', '=', 'chat_members.user_id')
                    ->leftJoin('uploads', 'uploads.id', '=', 'users.avatar_id')
                    ->where('chat_members.room_id', $this->room->id)
                    ->groupBy('users.id')
                    ->get();
         return $this->room;
    }
}
