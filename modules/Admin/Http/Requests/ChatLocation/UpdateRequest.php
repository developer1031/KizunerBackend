<?php

namespace Modules\Admin\Http\Requests\ChatLocation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function save($id)
    {
        $roomManager = EntityManager::getManager(RoomEntity::class);
        $room = $roomManager->find($id);
        $room->name = $this->name;
        $room->latitude     = $this->latitude;
        $room->longitude     = $this->longitude;
        return $room->save();
    }
}
