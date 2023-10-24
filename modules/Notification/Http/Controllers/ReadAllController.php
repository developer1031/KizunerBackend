<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReadAllController
{
    public function update()
    {
        $currentUserId = auth()->user()->id;
        DB::statement("UPDATE notification_notifications SET status = 1 WHERE user_id = '" . $currentUserId . "'");
        return response()->json([
            'data' => [
                'status' => true
            ]
        ], Response::HTTP_OK);
    }
}
