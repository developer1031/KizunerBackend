<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Queries\RoomMemberQuery;
use Modules\Chat\Domains\Actions\CreateGroupChatAction;
use Modules\Chat\Domains\Actions\CreatePersonalChatAction;

class RoomStoreRequest extends FormRequest
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
            'members' => 'required'
        ];
    }

    public function save()
    {
        $owner      = auth()->user()->id;
        $members    = $this->members;

        $room = is_array($members) ? (new CreateGroupChatAction($owner, $members))->execute() :
                                     (new CreatePersonalChatAction($owner, $members))->execute();
        return (new RoomMemberQuery($room))->execute();
    }
}
