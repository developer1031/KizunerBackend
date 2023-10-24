<?php

namespace Modules\Notification\Domains;

use Modules\Framework\Support\Facades\EntityManager;

class Notification
{

    public $notification;

    public function __construct(NotificationEntity $notification)
    {
        $this->notification = $notification;
    }

    public static function create(NotificationDto $noti)
    {
        $notification = EntityManager::create(NotificationEntity::class);
        $notification->user_id = $noti->getUserId();
        $notification->title = $noti->getTitle();
        $notification->body = $noti->getBody();
        $notification->payload = json_encode($noti->getPayload());
        $notification->type = $noti->getType();
        if ($noti->getUploadableId())  {
            $notification->uploadable_id = $noti->getUploadableId();
        }
        $notification->save();
        return $notification;
    }

    public static function updateStatus($id, $status)
    {
        $notiManager = EntityManager::getManager(NotificationEntity::class);
        $notification = $notiManager->find($id);
        $notification->status = $status;
        $notification->save();
        return $notification;
    }
}
