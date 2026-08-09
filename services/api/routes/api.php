<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\RidePriceSnapshotController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StripeController;
use App\Http\Controllers\AutoFleet\AutofleetClientWebHookController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\AutoFleet\AutoFleetWebHookController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\DriverScheduleController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\DriverSettingsController;
use App\Http\Controllers\EnchantController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:global'])->group(function () {

    // AUTHENTICATION Routes
    Route::prefix('auth')->group(function () {
        Route::prefix('driver')->group(function () {
            Route::prefix('sms')->group(function () {
                Route::post('login', [DriverAuthController::class, 'login'])->middleware(['throttle:sms']);
                Route::post('verify', [DriverAuthController::class, 'verify'])->middleware(['throttle:sms']);
            });

            Route::post('logout', [DriverAuthController::class, 'logout'])->middleware(['auth:sanctum', 'abilities:driver.portal']);
        });
    });

    // DRIVER Routes
    Route::prefix('driver')
        ->middleware(['auth:sanctum', 'abilities:driver.portal'])
        ->group(function () {
            Route::get('/settings', [DriverSettingsController::class, 'index']);
            Route::get('/settings/{key}', [DriverSettingsController::class, 'show']);

            Route::prefix('{driver}')
                ->group(function () {
                    Route::get('/', [DriverController::class, 'get']);

                    Route::prefix('schedule')->group(function () {
                        Route::get('/weekly', [DriverScheduleController::class, 'weekly']);
                        Route::get('/city', [DriverScheduleController::class, 'city']);
                        Route::post('/', [DriverScheduleController::class, 'store']);
                        Route::delete('/{schedule}', [DriverScheduleController::class, 'delete']);
                    });
                })
                ->scopeBindings();
        });

    Route::prefix('admin')->middleware('admin-key')->group(function () {
        Route::post('client/find', [ClientController::class, 'search']);
        Route::get('client/{id}/rides', [ClientController::class, 'ridesById']);
        Route::get('services/{id}', [ServiceController::class, 'show']);
        Route::get('stripe/payment-intent/{paymentId}', [StripeController::class, 'paymentIntent']);
        Route::get('stripe/charge/{chargeId}', [StripeController::class, 'charge']);
        Route::get('ride-price-snapshots', [RidePriceSnapshotController::class, 'index']);
    });
});

Route::prefix('enchant')->group(function () {
    Route::post('/customer', [EnchantController::class, 'customerView']);
});


Route::prefix('webhook')->group(function () {
    Route::post('/ride-updated', [AutoFleetWebHookController::class, 'rideUpdated']);
    Route::post('/driver-created', [AutoFleetWebHookController::class, 'driverCreation']);

    Route::post('/price-change', [AutoFleetWebHookController::class, 'priceChange']);
    Route::Post('/additional-charge-added', [AutoFleetWebHookController::class, 'additionalChargeAdded']);
    
    Route::post('/clients/onboarded', [AutofleetClientWebHookController::class, 'onboarded']);
    Route::post('/clients/updated', [AutofleetClientWebHookController::class, 'updated']);
    Route::post('/clients/deleted', [AutofleetClientWebHookController::class, 'deleted']);
});
