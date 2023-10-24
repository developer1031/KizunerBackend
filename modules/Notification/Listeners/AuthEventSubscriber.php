<?php

namespace Modules\Notification\Listeners;

use Carbon\Carbon;
use Modules\User\Events\Auth\UserLoginEvent;
use Modules\User\Events\Auth\UserLogoutEvent;

class AuthEventSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserLogoutEvent::class,
            'Modules\Notification\Listeners\AuthEventSubscriber@handleUserLogout'
        );

        $events->listen(
            UserLoginEvent::class,
            'Modules\Notification\Listeners\AuthEventSubscriber@handleUserLogin'
        );
    }

    public function handleUserLogout(UserLogoutEvent $event)
    {
        $user = $event->getObject();
        $user->fcm_token = null;
        $user->save();
    }

    public function handleUserLogin(UserLoginEvent $event)
    {
        $user = $event->getObject();
        $user->last_login = Carbon::now();
        $user->save();
    }
}
