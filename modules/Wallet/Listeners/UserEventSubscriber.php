<?php

namespace Modules\Wallet\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\User\Events\UserCreatedEvent;
use App\User;
use Modules\Wallet\Domains\Wallet;
use Modules\Wallet\Stripe\StripeCustomer;

class UserEventSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            UserCreatedEvent::class,
            'Modules\Wallet\Listeners\UserEventSubscriber@handleUserCreated'
        );
    }

    /**
     * Create a new Customer on Stripe and Wallet on our system
     * @param UserCreatedEvent $event
     */
    public function handleUserCreated(UserCreatedEvent $event)
    {
        // Don't use this directly, u not sure this user have latest information
        $userObj = $event->getObject();

        // Get Latest User information from Database
        $user    = User::find($userObj->id);

       try {
           $email = $user->email ? $user->email : $user->id . '@kizuner.app';
           // Create Stripe Customer
           $stripeCustomer = StripeCustomer::create($email, $user->name);
           // Create Wallet for New Registered User
           Wallet::create($user->id, $stripeCustomer->id);
       } catch (\Exception $e) {
           Log::error($e->getMessage());
       }
    }
}
