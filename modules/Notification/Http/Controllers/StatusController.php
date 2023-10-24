<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Notification\Domains\Notification;

class StatusController
{
    public function update(string $id)
    {
        Notification::updateStatus($id, true);
        return response()->json([
            'data' => [
                'status' => true
            ]
        ], Response::HTTP_OK);
    }
}
