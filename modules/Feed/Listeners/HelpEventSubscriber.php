<?php

namespace Modules\Feed\Listeners;

use App\User;
use Modules\Feed\Events\TimelineCreatedEvent;
use Modules\Helps\Events\HelpCreatedEvent;

class HelpEventSubscriber extends AbstractEventSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
//        $events->listen(
//            HelpCreatedEvent::class,
//            'Modules\Feed\Listeners\HelpEventSubscriber@handleHelpCreated'
//        );
    }

    public function handleHelpCreated(HelpCreatedEvent $event)
    {
//        $help = $event->getObject();
//        /** @var User $user */
//        $user = app('request')->user();
//
//        $this->feedTimelineRepository->create(
//            $user->id,
//            $help->id,
//            'help',
//            'new',
//            $help->user_id
//        );
    }
}
