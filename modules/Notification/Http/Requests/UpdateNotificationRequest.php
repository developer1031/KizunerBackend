<?php

namespace Modules\Notification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'notification' => 'required'
        ];
    }

    public function save()
    {
        $user = auth()->user();
        $user->notification = $this->notification;
        $user->email_notification = $this->email_notification ? $this->email_notification : 0;
        $user->save();
        return [
            'data' => [
                'notification' => $user->notification,
                'email_notification' => (bool)$user->email_notification,
            ]
        ];
    }
}
