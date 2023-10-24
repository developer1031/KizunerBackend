<?php

namespace Modules\Wallet\Providers;

use Carbon\Laravel\ServiceProvider;
use Stripe\Stripe;

class StripeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Stripe::setApiKey(config('services.stripe.stripe_secret'));
    }
}
