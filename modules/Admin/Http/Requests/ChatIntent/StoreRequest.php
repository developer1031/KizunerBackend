<?php

namespace Modules\Admin\Http\Requests\ChatIntent;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Models\ChatIntent;

class StoreRequest extends FormRequest
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
        $chat_intent = new ChatIntent($this->only(['intent', 'reply']));
        $chat_intent->save();
        return $chat_intent;
    }
}
