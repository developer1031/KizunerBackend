<?php

namespace Modules\Feed\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Feed\Models\Timeline;
use Modules\Feed\Observers\TimelineObserver;

class FeedServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Timeline::observe(TimelineObserver::class);
    }
}
