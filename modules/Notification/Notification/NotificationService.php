<?php

namespace Modules\Notification\Notification;

interface NotificationService
{
    public function sendBatchNotification($deviceTokens, $data);

    public function sendNotification($data);

    public function subscribeTopic($deviceTokens, $topicName);

    public function unsubscribeTopic($deviceTokens, $topicName);
}
