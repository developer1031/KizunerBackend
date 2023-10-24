<?php

namespace Modules\Notification\Http\Controllers;

use Modules\Notification\Http\Requests\StoreDeviceRequest;

class DeviceController
{
    public function store(StoreDeviceRequest $request)
    {
        $request->save();
        return response()->json([
            'data' => [
                'status' => true,
                'message' => 'Device Token Stored'
            ]
        ]);
    }
}
