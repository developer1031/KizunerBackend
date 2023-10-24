<?php

namespace Modules\Chat\Http\Controllers;

use App\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use Illuminate\Http\Response;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Member;
use Modules\Chat\Domains\Message;
use Modules\Chat\Domains\Queries\RoomMessagesQuery;
use Modules\Chat\Domains\Room;
use Modules\Chat\Http\Requests\MessageStoreRequest;
use Modules\Chat\Http\Transformers\MessageTransformer;
use Modules\Framework\Support\Requests\Pagination;

class MessageController
{
    public function index(string $roomId)
    {
        $perPage = app('request')->input('per_page');
        $perPage = Pagination::normalize($perPage);

        //update room
        $chat_members = Member::findByRoomId($roomId);
        foreach ($chat_members as $member) {
            $user_member = \Modules\User\Domains\User::find($member->user_id);
            if($user_member && $user_member->is_fake) {
                $is_fake = 1;
                Room::update($roomId, '', $is_fake);
                break;
            }
        }

        return response()
            ->json(fractal((new RoomMessagesQuery($roomId, $perPage))->execute(), new MessageTransformer()), Response::HTTP_OK);
    }

    public function store(MessageStoreRequest $request)
    {
        return response()
                ->json(fractal($request->save(), new MessageTransformer()), Response::HTTP_CREATED);
    }

    public function destroy(string $id)
    {
        return response()
                    ->json([
                        'data' => [
                            'status' => Message::delete($id)
                        ]
                    ], Response::HTTP_OK);
    }

    public function chatBot() {
        $botman = app('botman');
        $botman->listen();
    }
}
