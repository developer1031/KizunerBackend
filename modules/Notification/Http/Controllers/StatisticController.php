<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Notification\Domains\NotificationEntity;

class StatisticController
{
    public function index()
    {
        $notiManager = EntityManager::getManager(NotificationEntity::class);
        $notiCount = $notiManager->where('user_id', auth()->user()->id)
                                 ->where('status', false)
                                 ->count();
        return response()->json([
            'data' => [
                'statistic' => $notiCount
            ]
        ], Response::HTTP_OK);
    }
}
