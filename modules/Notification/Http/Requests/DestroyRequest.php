<?php

namespace Modules\Notification\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Notification\Domains\NotificationEntity;

class DestroyRequest extends FormRequest
{
    public function rules()
    {
        return [
            //
        ];
    }

    public function save()
    {
        if ($this->id == null) {
            DB::table('notification_notifications')
                ->where('user_id', auth()->user()->id)
                ->delete();
        } else {
            DB::table('notification_notifications')
                ->where('id', $this->id)
                ->delete();
        }
    }
}
