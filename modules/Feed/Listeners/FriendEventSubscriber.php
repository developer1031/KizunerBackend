<?php

namespace Modules\Feed\Listeners;

use Modules\Friend\Events\FriendAcceptedEvent;
use Modules\Friend\Events\FriendCreatedEvent;

class FriendEventSubscriber extends AbstractEventSubscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            FriendCreatedEvent::class,
            'Modules\Feed\Listeners\FriendEventSubscriber@handlerFriendCreated'
        );

        $events->listen(
            FriendAcceptedEvent::class,
            'Modules\Feed\Listeners\FriendEventSubscriber@handlerFriendAccepted'
        );
    }

    public function handlerFriendAccepted(FriendAcceptedEvent $event)
    {
        $friend = $event->getObject();

        $feedFl = $this->feedFollowRepository->create(
            $friend->friend_id,
            $friend->user_id
        );

        if ($feedFl->isInactive()) {
            $this->feedFollowRepository->updateStatus(
                $friend->friend_id,
                $friend->user_id,
                'active'
            );
        }

    }

    public function handlerFriendCreated(FriendCreatedEvent $event)
    {
        $friend = $event->getObject();

        $feedFl = $this->feedFollowRepository->create(
            $friend->user_id,
            $friend->friend_id
        );

        if ($feedFl->isInactive()) {
            $this->feedFollowRepository->updateStatus(
                $friend->user_id,
                $friend->friend_id,
                'active'
            );
        }
    }
}
