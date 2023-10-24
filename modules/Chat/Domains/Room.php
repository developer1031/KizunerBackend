<?php

namespace Modules\Chat\Domains;

use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Domains\Events\RoomDeletedEvent;
use Modules\Framework\Support\Facades\EntityManager;

class Room
{
    public $room;

    public function __construct(RoomEntity $room)
    {
        $this->room = $room;
    }

    public static function find(string $id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        return $roomManager->where('id', $id)->where('status', 'active')->first();
    }

    /**
     * @param string $type
     * @return mixed
     */
    public static function create(string $type, $is_fake=null)
    {
        $room = EntityManager::create(RoomEntity::class);
        $room->type     = $type;
        $room->status   = RoomEntity::STATUS_ACTIVE;

        if($is_fake)
            $room->is_fake = $is_fake;

        $room->save();
        return $room;
    }

    public static function update(string $id, string $name, $is_fake=null)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $room = $roomManager->find($id);
        $room->name = $name;

        if($is_fake)
            $room->is_fake = $is_fake;

        $room->save();
        return $room;
    }

    /**
     * @param string $id
     * @event RoomDeletedEvent
     * @return boolean
     */
    public static function delete(string $id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $check = $roomManager->destroy($id);

        // Dispatch Event to Clean Database After
        if ($check) event(New RoomDeletedEvent($id));

        return $check;
    }
}
