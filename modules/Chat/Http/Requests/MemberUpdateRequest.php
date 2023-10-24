<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Domains\Actions\UpdateGroupMemberAction;

class MemberUpdateRequest extends FormRequest
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

    public function save(string $roomId)
    {
        $members = $this->members;
        return (new UpdateGroupMemberAction($roomId, $members))->execute();
    }
}
