<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;
use Modules\Framework\Service\Facade\Environment;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{

    public function register()
    {
        Horizon::auth(function() {
            $urlParts = explode('.', $_SERVER['HTTP_HOST']);
            $subdomain = $urlParts[0];

            if (Environment::isProduction() && $subdomain !== 'admin') {
                abort(404);
            }
            return true;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');

        // Horizon::night();
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewHorizon', function ($user) {
            return in_array($user->email, [
                'admin@admin.com'
            ]);
        });
    }
}
