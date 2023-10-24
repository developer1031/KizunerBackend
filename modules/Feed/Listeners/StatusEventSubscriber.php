<?php

namespace Modules\Feed\Listeners;

use Modules\Status\Events\StatusCreatedEvent;
use Modules\Status\Events\StatusDeletedEvent;

class StatusEventSubscriber extends AbstractEventSubscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            StatusCreatedEvent::class,
            'Modules\Feed\Listeners\StatusEventSubscriber@handleStatusCreated'
        );

        $events->listen(
            StatusDeletedEvent::class,
            'Modules\Feed\Listeners\StatusEventSubscriber@handleStatusDeleted'
        );

    }

    public function handleStatusDeleted(StatusDeletedEvent $event)
    {
        $object = (string)$event->getObject();

        $this->feedTimelineRepository
            ->deleteByReference($object);
    }

    public function handleStatusCreated(StatusCreatedEvent $event)
    {
        $status = $event->getObject();
        $user = app('request')->user();

        $timeline = $this->feedTimelineRepository->create(
            $user->id,
            $status->id,
            'status',
            'new',
            $status->user_id
        );
    }
}
