<?php

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Framework\Support\Facades\EntityManager;
use Modules\Framework\Support\Requests\Pagination;
use Modules\Notification\Domains\NotificationEntity;
use Modules\Notification\Domains\UserNotificationQuery;
use Modules\Notification\Http\Requests\DestroyRequest;
use Modules\Notification\Http\Requests\UpdateNotificationRequest;
use Modules\Notification\Http\Transformers\NotificationTransformer;

class NotificationController
{

    public function update(UpdateNotificationRequest $request)
    {
        if ($request->validated()) {
            return response()->json(
                $request->save(),
                Response::HTTP_OK
            );
        }
    }

    public function show()
    {
        return response()->json([
            'data' => [
                'notification' => (bool)auth()->user()->notification,
                'email_notification' => (bool)auth()->user()->email_notification,
            ]
        ], Response::HTTP_OK);
    }

    public function index()
    {
        $perPage = Pagination::normalize(app('request')->input('per_page'));
        $currentUserId = auth()->user()->id;

        return response()->json(
            fractal((new UserNotificationQuery($currentUserId, $perPage))->execute(), new NotificationTransformer()),
            Response::HTTP_OK
        );

    }

    public function destroy(DestroyRequest $request)
    {
        $request->save();
        return response()->json([
            'data' => [
                'status' => true
            ]
        ], Response::HTTP_OK);
    }
}
