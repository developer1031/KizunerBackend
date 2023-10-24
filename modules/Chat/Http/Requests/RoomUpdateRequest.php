<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Queries\RoomMemberQuery;
use Modules\Chat\Domains\Room;

class RoomUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable'
        ];
    }

    public function save(string $id)
    {
        $name = $this->name;
        $room = Room::update($id, $name);
        return (new RoomMemberQuery($room))->execute();
    }
}
