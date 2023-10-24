<?php

namespace Modules\Notification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeviceRequest extends FormRequest
{
    public function rules()
    {
        return [
            'fcm_token' => 'required',
        ];
    }

    public function save()
    {
        $user = auth()->user();
        $user->fcm_token = $this->fcm_token;
        $user->save();
    }
}
