<?php

namespace App\Providers;

use App\Models\Clients\Client;
use App\Models\Driver;
use App\Observers\Chatwoot\ChatwootClientObserver;
use App\Observers\Zoho\ZohoClientObserver;
use App\Policies\DriverPolicy;
use App\Services\AutoFleetService;
use App\Services\DriverService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AutoFleetService::class);
        $this->app->singleton(DriverService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Driver::class, DriverPolicy::class);

        // Only attach the observer if the feature is explicitly enabled
        if (config('features.zoho_rider_sync')) {
            Client::observe(ZohoClientObserver::class);
        }

        if (config('features.chatwoot_client_sync')) {
            Client::observe(ChatwootClientObserver::class);
        }

        RateLimiter::for(
            'global',
            fn(Request $request) =>
            Limit::perMinute(10000)->by($request->ip())
        );

        RateLimiter::for(
            'sms',
            fn(Request $request) =>
            Limit::perMinute(10)->by($request->ip())
        );
    }
}
