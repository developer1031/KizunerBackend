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
use Illuminate\Http\Request;

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

  public function hangout_help_notification(Request $request)
  {
    $user = auth()->user();
    $user->hangout_help_notification = $request->get('hangout_help_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'hangout_help_notification' => (bool)$user->hangout_help_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function message_notification(Request $request)
  {
    $user = auth()->user();
    $user->message_notification = $request->get('message_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'message_notification' => (bool)$user->message_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function follow_notification(Request $request)
  {
    $user = auth()->user();
    $user->follow_notification = $request->get('follow_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'follow_notification' => (bool)$user->follow_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function comment_notification(Request $request)
  {
    $user = auth()->user();
    $user->comment_notification = $request->get('comment_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'comment_notification' => (bool)$user->comment_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function like_notification(Request $request)
  {
    $user = auth()->user();
    $user->like_notification = $request->get('like_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'like_notification' => (bool)$user->like_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function payment_email_notification(Request $request)
  {
    $user = auth()->user();
    $user->email_notification = $request->get('payment_email_notification');
    $user->save();
    return response()->json(
      [
        'data' => [
          'payment_email_notification' => (bool)$user->email_notification
        ]
      ],
      Response::HTTP_OK
    );
  }

  public function show()
  {
    return response()->json([
      'data' => [
        'hangout_help_notification' => (bool)auth()->user()->hangout_help_notification,
        'message_notification' => (bool)auth()->user()->message_notification,
        'follow_notification' => (bool)auth()->user()->follow_notification,
        'comment_notification' => (bool)auth()->user()->comment_notification,
        'like_notification' => (bool)auth()->user()->like_notification,
        'hangout_help_email_notification' => (bool)auth()->user()->hangout_help_email_notification,
        'message_email_notification' => (bool)auth()->user()->message_email_notification,
        'follow_email_notification' => (bool)auth()->user()->follow_email_notification,
        'comment_email_notification' => (bool)auth()->user()->comment_email_notification,
        'like_email_notification' => (bool)auth()->user()->like_email_notification,
        'payment_email_notification' => (bool)auth()->user()->email_notification, // for payment
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