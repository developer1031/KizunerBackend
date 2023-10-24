<?php

namespace Modules\Chat\Domains\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Message;
use Modules\Chat\Http\Transformers\MessageTransformer;

class RoomSearchQuery
{

    private $perPage;

    private $query;

    private $userId;
    private $type;

    public function __construct(string $userId, string $query, int $perPage, $type=null)
    {
        $this->query        = $query;
        $this->perPage      = $perPage;
        $this->userId       = $userId;
        $this->type         = $type;
    }

    public function execute()
    {
        $roomIds    = $this->getUserRoomIds();
        $current_user = auth()->user();

        if($this->type) {
            if($this->type==RoomEntity::TYPE_LOCATION) {

                $chatRooms = DB::table('chat_rooms')
                    ->select('*')
                    ->selectRaw('(6371 * acos (
                                              cos ( radians('  . $current_user->location->lat .  ') )
                                              * cos( radians( chat_rooms.latitude ) )
                                              * cos( radians( chat_rooms.longitude ) - radians('  . $current_user->location->lng .  ') )
                                              + sin ( radians(' . $current_user->location->lat . ') )
                                              * sin( radians( chat_rooms.latitude ) )
                                            )
                                ) AS distance')

                    //->whereIn('id', $roomIds)
                    ->where('type', $this->type)
                    ->where('name', 'like', '%'. $this->query .'%')

                    ->having('distance', '<=', 50)

                    ->orderBy('updated_at')
                    ->paginate($this->perPage);
            }

            else if($this->type==RoomEntity::TYPE_PUBLIC_GROUP) {
                $chatRooms = DB::table('chat_rooms')
                    ->whereIn('id', $roomIds)
                    ->where('type', $this->type)
                    ->orWhere('name', 'like', '%'. $this->query .'%')
                    ->orderBy('updated_at')
                    ->paginate($this->perPage);
            }

            else if($this->type==RoomEntity::TYPE_GROUP) {
                $chatRooms = DB::table('chat_rooms')
                    //->whereIn('id', $roomIds)
                    ->where('type', $this->type)
                    ->where('name', 'like', '%'. $this->query .'%')
                    ->orderBy('updated_at')
                    ->paginate($this->perPage);
            }

            else {
                $chatRooms = DB::table('chat_rooms')
                    ->whereIn('id', $roomIds)
                    ->where('type', $this->type)
                    //->where('name', 'like', '%'. $this->query .'%')
                    ->orderBy('updated_at')
                    ->paginate($this->perPage);
            }

            /*
            $chatRooms = DB::table('chat_rooms')
                ->whereIn('id', $roomIds)
                ->where('type', $this->type)
                ->orWhere('name', 'like', '%'. $this->query .'%')
                ->orderBy('updated_at')
                ->paginate($this->perPage);
            */
        }
        else {
            $chatRooms = DB::table('chat_rooms')
                ->whereIn('id', $roomIds)
                ->orWhere('name', 'like', '%'. $this->query .'%')
                ->orderBy('updated_at')
                ->paginate($this->perPage);
        }

        $chatRooms->each(function ($item) {
            $item->users            = $this->getRoomUsers($item->id);
            $lastMessage            = Message::getLastMessageByRoomId($item->id);
            $item->last_message     = null;
            if ($lastMessage) {
                $item->last_message = fractal((new MessageQuery($lastMessage->id))->execute(), new MessageTransformer());
            }
        });

        return $chatRooms;

    }

    private function getUserRoomIds()
    {
        $userIds = $this->getUserIdsWithSearchTerm();

        $currentUserRooms = DB::table('chat_members')
                            ->select('room_id')
                            ->where('user_id', auth()->user()->id)
                            ->groupBy('room_id')
                            ->get()
                            ->pluck('room_id')
                            ->toArray();

        return DB::table('chat_members')
            ->select('room_id')
            ->whereIn('user_id', $userIds)
            ->groupBy('room_id')
            ->get()
            ->filter(function($item) use ($currentUserRooms) {
                return in_array($item->room_id, $currentUserRooms);
            })
            ->pluck('room_id')
            ->toArray();
    }

    private function getUserIdsWithSearchTerm()
    {
        return DB::table('users')
                ->select('id')
                ->where('name', 'like', '%'.$this->query.'%')
                ->where('id', '<>', auth()->user()->id)
                ->get()
                ->pluck('id')
                ->toArray();
    }

    private function getRoomUsers(string $roomId)
    {
        return DB::table('chat_members')
            ->select(
                'users.id as user_id',
                'users.name as user_name',
                'users.online as online',
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
