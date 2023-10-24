<?php

namespace Modules\Chat\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Chat\Domains\Entities\MemberEntity;

class SeenController
{
    public function update(string $roomId)
    {
        $userId = auth()->user()->id;
        $currentTime = Carbon::now();

        $memberInRoom = MemberEntity::where([
            'room_id' => $roomId,
            'user_id' => $userId
        ])->first();

        $memberInRoom->seen_at = $currentTime;
        $memberInRoom->save();
        return response()->json([
            'data' => [
                'room_id' => $roomId,
                'user_id' => $userId,
                'seen_at' => $currentTime
            ]
        ], Response::HTTP_OK);
    }
}
