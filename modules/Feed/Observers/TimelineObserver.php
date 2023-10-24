<?php

namespace Modules\Feed\Observers;

use Modules\Feed\Contracts\Data\TimelineInterface;
use Modules\Feed\Events\NewTimelineCreated;

class TimelineObserver
{
    public function created(TimelineInterface $timeline)
    {
        event(new NewTimelineCreated($timeline));
    }
}
