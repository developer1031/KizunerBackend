<?php

namespace Modules\Chat\Domains\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Message;
use Modules\Chat\Domains\Repositories\Contracts\MemberRepositoryInterface;
use Modules\Chat\Http\Transformers\MessageTransformer;

class UserRoomsQuery
{

    private $userId;

    private $memberRepository;

    private $perPage;
    private $type;

    public function __construct(string $userId, $perPage, $type=null)
    {
        $this->memberRepository = resolve(MemberRepositoryInterface::class);
        $this->userId           = $userId;
        $this->perPage          = $perPage;
        $this->type             = $type;
    }

    public function execute()
    {
        return $this->getChatRooms();
    }

    private function getChatRooms()
    {
        if($this->type) {
            if($this->type==RoomEntity::TYPE_LOCATION) {
                $current_user = auth()->user();
                $chatRooms = DB::table('chat_rooms')
                    ->select(['*', 'id as room_id'])
                    ->selectRaw('(6371 * acos (
                                              cos ( radians('  . $current_user->location->lat .  ') )
                                              * cos( radians( chat_rooms.latitude ) )
                                              * cos( radians( chat_rooms.longitude ) - radians('  . $current_user->location->lng .  ') )
                                              + sin ( radians(' . $current_user->location->lat . ') )
                                              * sin( radians( chat_rooms.latitude ) )
                                            )
                                ) AS distance')
                    ->where('chat_rooms.type', $this->type)
                    ->having('distance', '<=', 50)
                    ->orderBy('chat_rooms.updated_at', 'desc')
                    ->paginate($this->perPage);
            }
            else if($this->type==RoomEntity::TYPE_PUBLIC_GROUP) {
                $chatRooms = DB::table('chat_rooms')
                    ->select(['*', 'id as room_id'])
                    ->where('chat_rooms.type', $this->type)
                    ->orderBy('chat_rooms.updated_at', 'desc')
                    ->paginate($this->perPage);
            }
            else {
                $chatRooms = DB::table('chat_members')
                    ->join('chat_rooms', 'chat_members.room_id', '=', 'chat_rooms.id')
                    ->where('chat_members.user_id', $this->userId)
                    ->where('chat_rooms.type', $this->type)
                    ->orderBy('chat_rooms.updated_at', 'desc')
                    ->paginate($this->perPage);
            }
        }
        else {
            $chatRooms = DB::table('chat_members')
                ->join('chat_rooms', 'chat_members.room_id', '=', 'chat_rooms.id')
                //->leftJoin('users', 'users.id', '=', 'chat_members.user_id')
                ->where('chat_members.user_id', $this->userId)

                /*
                ->where(function($query) {
                     $query->whereNull('chat_rooms.is_fake');
                     $query->orWhere('chat_rooms.is_fake', 0);
                 })
                */

                ->orderBy('chat_rooms.updated_at', 'desc')
                ->paginate($this->perPage);
        }

        $chatRooms->each(function ($item) {

             $item->users            = $this->getRoomUsers($item->room_id);

             $lastMessage            = Message::getLastMessageByRoomId($item->room_id);
             $item->last_message     = null;
             if ($lastMessage) {
                 $item->last_message = fractal((new MessageQuery($lastMessage->id))->execute(), new MessageTransformer());
             }
         });

         return $chatRooms;
    }

    private function getRoomUsers(string $roomId)
    {
        return DB::table('chat_members')
                ->select(
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.online as online',
                    'users.is_fake as is_fake',
                    'users.is_fake as user_is_fake',
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
