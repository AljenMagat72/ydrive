<?php

namespace App\Providers;

use App\Models\Driver;
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

    RateLimiter::for(
      'global', 
      fn(Request $request) => 
      Limit::perMinute(10000)->by($request->ip()));

    RateLimiter::for(
      'sms',
      fn(Request $request) =>
      Limit::perMinute(10)->by($request->ip())
    );
  }
}
