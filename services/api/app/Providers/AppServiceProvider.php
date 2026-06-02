<?php

namespace App\Providers;

use App\Models\Clients\Client;
use App\Models\Driver;
use App\Observers\Chatwoot\ChatwootClientObserver;
use App\Observers\Zoho\ZohoClientObserver;
use App\Policies\DriverPolicy;
use App\Services\AutoFleetService;
use App\Services\Driver\DriverService;
use App\Services\Support\TokenService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AutoFleetService::class);
        $this->app->singleton(DriverService::class);


        $this->app->singleton('token', function () {
            return new TokenService();
        });

        Route::macro('mount', function (string $name, array $middleware = []) {
            $routes = fn() => require base_path("routes/frontend/{$name}.php");

            if (app()->environment('local')) {
                return Route::prefix($name)->middleware($middleware)->group($routes);
            }

            return Route::domain("$name.".parse_url(config('app.url'), PHP_URL_HOST))->middleware($middleware)->group($routes);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
        Model::preventSilentlyDiscardingAttributes();

        Gate::policy(Driver::class, DriverPolicy::class);

        View::addNamespace('admin', base_path('resources/frontend/admin'));

        if (config('features.zoho_rider_sync')) {
            Client::observe(ZohoClientObserver::class);
        }

        if (config('features.chatwoot_client_sync')) {
            Client::observe(ChatwootClientObserver::class);
        }

        RateLimiter::for(
            'global',
            fn(Request $request) =>
            Limit::perMinute(1000)->by($request->ip())
        );

        RateLimiter::for(
            'sms',
            fn(Request $request) =>
            Limit::perMinute(10)->by($request->ip())
        );
    }
}
