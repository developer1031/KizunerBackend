<?php

namespace Modules\Admin\Http\Requests\ChatLocation;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Entities\RoomEntity;
use Modules\Chat\Models\ChatIntent;
use Modules\Framework\Support\Facades\EntityManager;

class StoreRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    public function save()
    {
        $room = EntityManager::create(RoomEntity::class);
        $room->name     = $this->name;
        $room->latitude     = $this->latitude;
        $room->longitude     = $this->longitude;
        $room->type     = RoomEntity::TYPE_LOCATION;
        $room->status   = RoomEntity::STATUS_ACTIVE;
        return $room->save();
    }
}
