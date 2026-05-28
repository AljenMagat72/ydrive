<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StripeController;
use App\Http\Controllers\AutoFleet\AutofleetClientWebHookController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\AutoFleet\AutoFleetWebHookController;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\Driver\DriverDelinquentController;
use App\Http\Controllers\Driver\DriverScheduleController;
use App\Http\Controllers\Driver\DriverAuthController;
use App\Http\Controllers\Driver\ScheduleController;
use App\Http\Controllers\EnchantController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::prefix('v1')->middleware(['throttle:global'])->group(function () {

    // AUTHENTICATION Routes
    Route::prefix('auth')->group(function () {
        Route::prefix('driver')->group(function () {
            Route::prefix('sms')->group(function () {
                Route::post('login', [DriverAuthController::class, 'login'])->middleware(['throttle:sms']);
                Route::post('verify', [DriverAuthController::class, 'verify'])->middleware(['auth:sanctum', 'abilities:auth.driver.verify']);
                Route::post('resend', [DriverAuthController::class, 'resend'])->middleware(['auth:sanctum', 'abilities:auth.driver.verify']);
            });

            Route::post('logout', [DriverAuthController::class, 'logout'])->middleware(['auth:sanctum', 'abilities:driver.portal']);
        });
    });

    // DRIVER Routes
    Route::prefix('driver')->middleware(['auth:sanctum', 'abilities:driver.portal'])->group(function () {
        Route::get('/me', [DriverAuthController::class, 'me'])->middleware(['throttle:sms'])->middleware(['auth:sanctum']);
        Route::get('/test/{driver}', [DriverAuthController::class, 'read'])->middleware(['auth:sanctum'])->middleware(['can:read,driver']);

        Route::prefix('schedule')->group(function () {
            Route::get('/weekly', [DriverScheduleController::class, 'weekly']);
            Route::post('/', [DriverScheduleController::class, 'store']);
            Route::delete('/', [DriverScheduleController::class, 'delete']);
        });
    });

    Route::prefix('admin')->middleware('admin-key')->group(function () {
        Route::get('driver/schedule/daily', [DriverScheduleController::class, 'daily']);
        Route::get('driver/schedule/delinquents', [DriverScheduleController::class, 'delinquents']);

        Route::post('driver/{id}/delinquent/revert', [DriverDelinquentController::class, 'revert']);
        Route::post('driver/{id}/delinquent/prevent', [DriverDelinquentController::class, 'prevent']);

        Route::post('client/find', [ClientController::class, 'search']);
        Route::get('client/{id}/rides', [ClientController::class, 'ridesById']);
        Route::get('services/{id}', [ServiceController::class, 'show']);
        Route::get('stripe/payment-intent/{paymentId}', [StripeController::class, 'paymentIntent']);
        Route::get('stripe/charge/{chargeId}', [StripeController::class, 'charge']);
    });

    // ADMIN Routes
    Route::middleware(['auth:sanctum', 'abilities:admin.portal'])->group(function () {
        Route::post('driver/schedule/store', [AdminScheduleController::class, 'store']);

        Route::get('driver/schedule/daily', [DriverScheduleController::class, 'daily']);
        Route::get('driver/schedule/range', [DriverScheduleController::class, 'range']);
        Route::get('driver/all', [DriverScheduleController::class, 'all']);
        Route::get('driver/schedule/delinquents', [DriverScheduleController::class, 'delinquents']);

        Route::post('driver/{id}/delinquent/revert', [DriverDelinquentController::class, 'revert']);
        Route::post('driver/{id}/delinquent/prevent', [DriverDelinquentController::class, 'prevent']);

        Route::post('client/find', [ClientController::class, 'search']);
        Route::get('client/{id}/rides', [ClientController::class, 'ridesById']);

        Route::delete('driver-schedule/{id}', [DriverScheduleController::class, 'deleteSplitSchedule']);

        Route::patch('driver/{id}/update-schedule', [DriverScheduleController::class, 'updateSchedule']);
    });

    // SHARED Routes
    Route::prefix('driver')->middleware(['auth:sanctum', 'ability:driver.portal,admin.portal'])->group(function () {
        Route::prefix('schedule')->group(function () {
            Route::get('/city', [DriverScheduleController::class, 'dailyCity']);
            Route::get('/weekly', [DriverScheduleController::class, 'weekly']);
            Route::post('/', [DriverScheduleController::class, 'store']);
            Route::delete('/', [DriverScheduleController::class, 'delete']);
            Route::post('/add', [ScheduleController::class, 'add']);
            Route::delete('driver-schedule/{id}', [DriverScheduleController::class, 'deleteSplitSchedule']);
        });

        Route::prefix('vendor')->group(function () {
            Route::get('/all', [VendorController::class, 'all']);
            Route::post('/update', [VendorController::class, 'update']);
            Route::get('/get', [VendorController::class, 'get']);
            Route::post('/revert', [VendorController::class, 'revert']);
        });
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


// SESSION Routes
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
});
