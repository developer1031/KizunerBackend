<?php

namespace Modules\Feed\Listeners;

use Modules\Feed\Contracts\Data\TimelineInterface;
use Modules\Feed\Models\Timeline;
use Modules\Friend\Events\BlockCreatedEvent;

class BlockEventSubscriber extends AbstractEventSubscriber
{

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            BlockCreatedEvent::class,
            'Modules\Feed\Listeners\BlockEventSubscriber@handlerBlockCreated'
        );
    }

    public function handlerBlockCreated(BlockCreatedEvent $event)
    {
        $block = $event->getObject();

        $this->feedFollowRepository->updateStatus(
            $block->user_id,
            $block->block_id,
            'inactive'
        );

        $this->feedFollowRepository->updateStatus(
            $block->block_id,
            $block->user_id,
            'inactive'
        );
    }
}
