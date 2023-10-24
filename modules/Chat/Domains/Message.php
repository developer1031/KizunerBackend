<?php

namespace Modules\Chat\Domains;

use Modules\Chat\Domains\Dto\MessageDto;
use Modules\Chat\Domains\Entities\MessageEntity;
use Modules\Framework\Support\Facades\EntityManager;

class Message
{
    public $message;

    public function __construct(MessageEntity $message)
    {
        $this->message = $message;
    }

    public static function create(MessageDto $messageDto)
    {
        $message = EntityManager::create(MessageEntity::class);
        $message->user_id       = $messageDto->userId;
        $message->room_id       = $messageDto->roomId;
        $message->text          = $messageDto->text;
        $message->hangout       = $messageDto->hangout;
        $message->help          = $messageDto->help;
        $message->is_fake       = $messageDto->is_fake;
        $message->related_user  = $messageDto->related_user;
        $message->save();
        return $message;
    }

    public static function delete(string $id)
    {
        $messageManager = EntityManager::getManager(MessageEntity::class);
        return $messageManager->destroy($id) === 1 ? true : false;
    }

    public static function getLastMessageByRoomId(string $roomId)
    {
        $messageManager = EntityManager::getManager(MessageEntity::class);
        return $messageManager->where('room_id', $roomId)->latest('created_at')->first();
    }
}
