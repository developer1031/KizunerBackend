<?php

namespace Modules\Feed\Listeners;

use App\User;
use Modules\Config\Config;
use Modules\Feed\Events\TimelineCreatedEvent;
use Modules\Hangout\Events\HangoutCreatedEvent;
use Modules\Hangout\Events\HangoutDeletedEvent;
use Modules\Helps\Events\HelpCreatedEvent;
use Modules\Kizuner\Models\LeaderBoard;

class HangoutEventSubscriber extends AbstractEventSubscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            HangoutCreatedEvent::class,
            'Modules\Feed\Listeners\HangoutEventSubscriber@handleHangoutCreated'
        );

        $events->listen(
            HangoutDeletedEvent::class,
            'Modules\Feed\Listeners\HangoutEventSubscriber@handleHangoutDeleted'
        );
    }

    public function handleHangoutDeleted(HangoutDeletedEvent $event)
    {
        $object = (string)$event->getObject();
        $this->feedTimelineRepository
            ->deleteByReference($object);
    }

    public function handleHangoutCreated(HangoutCreatedEvent $event)
    {
        $hangout = $event->getObject();
        /** @var User $user */
        $user = app('request')->user();

        //Add 30kz if First add
        $config_data = new Config();
        $kz = $config_data->getConfigValWithDefault('kizuner_first_add_post');
        addKizuna($user, $kz);

        $this->feedTimelineRepository->create(
            $user->id,
            $hangout->id,
            'hangout',
            'new',
            $hangout->user_id
        );
    }
}
