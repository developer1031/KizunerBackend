<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Queries\RoomMemberQuery;
use Modules\Chat\Domains\Room;
use Modules\Chat\Http\Requests\MemberUpdateRequest;
use Modules\Chat\Http\Transformers\RoomTransformer;

class MemberController
{
    public function update(string $roomId, MemberUpdateRequest $request)
    {
        return response()
                ->json(fractal($request->save($roomId), new RoomTransformer()), Response::HTTP_OK);
    }

    public function destroy(string $roomId, string $memberId)
    {
        Member::deleteByRoomIdAndMemberId($roomId, $memberId);
        return response()
                ->json(fractal((new RoomMemberQuery(Room::find($roomId)))->execute(), new RoomTransformer()), Response::HTTP_OK);
    }
}
