<?php

namespace Modules\Feed\Listeners;

use Modules\Friend\Events\FollowerCreatedEvent;
use \Illuminate\Events\Dispatcher;

class FollowEventSubscriber extends AbstractEventSubscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            FollowerCreatedEvent::class,
            'Modules\Feed\Listeners\FollowEventSubscriber@handlerFollowerCreated'
        );
    }

    /**
     * @param FollowerCreatedEvent $event
     */
    public function handlerFollowerCreated(FollowerCreatedEvent $event)
    {
        $follow = $event->getObject();

        $feedFl = $this->feedFollowRepository->create(
            $follow->getUserId(),
            $follow->getFollowId()
        );

        if ($feedFl->isInactive()) {
            $this->feedFollowRepository->updateStatus(
                $follow->getUserId(),
                $follow->getFollowId(),
                'active'
            );
        }
    }
}
