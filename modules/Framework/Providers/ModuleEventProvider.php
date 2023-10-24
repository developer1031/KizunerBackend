<?php

namespace Modules\Framework\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Framework\Service\Loader\EventLoader;

class ModuleEventProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $eventCollection = (new EventLoader())->getCollection();

        $eventCollection['listeners']->each(function($item) {
            Event::listen($item['name'], $item['handler']);
        });

        $eventCollection['subscribers']->each(function($item) {
            Event::subscribe($item['name']);
        });

    }

    public function register()
    {
        //
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        $eventCollection = (new EventLoader())->getCollection();
        return $eventCollection['discovers']->pluck('path')->toArray();
    }

    public function shouldDiscoverEvents()
    {
        return true;
    }
}
