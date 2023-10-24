<?php

namespace Modules\Chat\Http\Transformers;

use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class RoomTransformer extends TransformerAbstract
{
    public function transform($room)
    {
        $disk = \Storage::disk('gcs');


        $current_user = auth()->user();
        $fectchUserId = "";
        if (isset($current_user)) {
            $fectchUserId = auth()->user()->id;
        }

        return [
            "id"            => isset($room->room_id)  ? $room->room_id : $room->id,
            "created_at"    => Carbon::create(date($room->created_at)),
            "updated_at"    => Carbon::create(date($room->updated_at)),
            "name"          => $room->name,
            "type"          => $room->type,
            "status"        => $room->status,
            "fectch_user_id"        => $fectchUserId,
            "users"         => $this->normalizeUsers($room->users),
            "last_message"  => isset($room->last_message) ? $room->last_message : null,
            "avatar"        => isset($room->avatar) ? $disk->url($room->avatar) : null
        ];
    }

    private function normalizeUsers($users)
    {
        $result = [];

        foreach ($users as $user) {
            $temp = [
                "id"            => $user->user_id,
                "name"          => $user->user_name,
                "avatar"        => (isset($user->is_fake) && $user->is_fake) ? $user->fake_avatar : ($user->user_avatar != null ? \Storage::disk('gcs')->url($user->user_avatar) : null),
                "owner"         => $user->user_id,
                "online"        => $user->online,
                "seen_at"       => Carbon::create($user->seen_at),
                "is_fake"       => ($user && isset($user->is_fake)) ? $user->is_fake : false,
            ];
            $result[] = $temp;
        }
        return $result;
    }
}

