<?php

use App\Http\Middleware\HandleAdminKey;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use App\Models\City;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
      'abilities' => CheckAbilities::class,
      'ability' => CheckForAnyAbility::class,
      'admin-key' => HandleAdminKey::class
    ]);

    $middleware->web(append: [
      HandleAppearance::class,
      HandleInertiaRequests::class,
      AddLinkHeadersForPreloadedAssets::class,
    ]);
    $middleware->validateCsrfTokens(except: [
      'api/webhook/driver-created'
    ]);
  })

  ->withExceptions(function (Exceptions $exceptions) {
    $exceptions->shouldRenderJsonWhen(function ($request, $e) {
        if ($request->is('api/*')) {
            return true;
        }
        return $request->expectsJson();
    });
  })
  
  ->withSchedule(function (Schedule $schedule) {
    $cities = config('autofleet.cities');

    /*foreach ($cities as $cityName => $city) {
      $schedule->command("driver:warn-minimum-hours \"$cityName\"")
        ->timezone($city['timezone'])
        ->wednesdays()
        ->at('17:00')
        ->withoutOverlapping();

      $schedule->command("driver:warn-acceptance-rate \"$cityName\"")
        ->timezone($city['timezone'])
        ->thursdays()
        ->at('20:00')
        ->withoutOverlapping();

      $schedule->command("driver:move-drivers \"$cityName\"")
        ->timezone($city['timezone'])
        ->fridays()
        ->at('08:00')
        ->withoutOverlapping();
    }*/

    $schedule->command('driver:update-acceptance-rate')
      ->dailyAt('00:00')
      ->withoutOverlapping();
  })
  ->create();
