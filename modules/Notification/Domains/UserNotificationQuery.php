<?php

namespace Modules\Notification\Domains;

use Modules\Framework\Support\Facades\EntityManager;

class UserNotificationQuery
{
    private $userId;

    private $perPage;

    /**
     * UserNotificationQuery constructor.
     * @param $userId
     * @param $perPage
     */
    public function __construct($userId, $perPage)
    {
        $this->userId = $userId;
        $this->perPage = $perPage;
    }

    public function execute()
    {
        $notificationManager = EntityManager::getManager(NotificationEntity::class);

        return $notificationManager
                    ->select(
                        'notification_notifications.id as id',
                        'notification_notifications.title as title',
                        'notification_notifications.body as body',
                        'notification_notifications.payload as payload',
                        'notification_notifications.created_at as created_at',
                        'notification_notifications.status as status',
                        'u.thumb as image'
                    )
                    ->where('user_id', $this->userId)
                    ->leftJoin('uploads as u', 'u.id', '=', 'notification_notifications.uploadable_id')
                    ->orderBy('notification_notifications.id', 'desc')
                    ->groupBy('notification_notifications.id')
                    ->paginate($this->perPage);
    }

}
