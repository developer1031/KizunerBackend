<?php

namespace Modules\Chat\Http\Controllers;

use Illuminate\Http\Request;

class StatusController
{
    public function update(Request $request)
    {
        $user = auth()->user();
        $user->online = $request->online;
        $user->save();
        return response()->json([
            'status' => (boolean)$user->online
        ]);
    }
}
