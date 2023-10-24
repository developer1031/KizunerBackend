<?php

namespace Modules\Admin\Http\Requests\ChatIntent;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Models\ChatIntent;

class UpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'intent' => 'required',
            'reply' => 'required'
        ];
    }

    public function save()
    {
        $chat_intent = ChatIntent::find($this->id);
        $chat_intent->fill($this->only(['intent', 'reply']));
        $chat_intent->save();
        return $chat_intent;
    }
}
