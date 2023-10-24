<?php

namespace Modules\Chat\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Actions\CreateMessageAction;
use Modules\Chat\Domains\Dto\MessageDto;
use Modules\Chat\Domains\Entities\MemberEntity;
use Modules\Chat\Domains\Queries\MessageQuery;
use Modules\Chat\Domains\Room;

class MessageStoreRequest extends FormRequest
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
            'room_id'       => 'required',
            'text'          => 'nullable',
            'hangout'       => 'nullable',
            'help'       => 'nullable',
            'images'        => 'nullable'
        ];
    }


    public function save()
    {
        $user = auth()->user();

        $messageDto = new MessageDto(
                                $user->id,
                                $this->room_id,
                                $this->text,
                                $this->hangout,
                                $this->help,
                                $this->images
                            );

        $chatRoom = Room::find($this->room_id);
        $chatRoom->updated_at = Carbon::now();
        $chatRoom->save();

        $chatMembers = MemberEntity::where([
            'room_id' => $this->room_id,
            'user_id' => $user->id
        ])->first();
        $chatMembers->seen_at = Carbon::now();
        $chatMembers->save();

        $message = (new CreateMessageAction($messageDto))->execute();
        return (new MessageQuery($message->id))->execute();
    }
}
