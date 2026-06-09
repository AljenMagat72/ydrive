<?php

use App\Filament\Resources\Drivers\Components\ScheduleDrivers;
use App\Http\Middleware\HandleAdminKey;
use App\Http\Middleware\HandleDriverInertiaRequests;

Route::mount('driver', [HandleDriverInertiaRequests::class]);

Route::prefix('widgets')->middleware([HandleAdminKey::class])->group(function() {
    Route::get('/schedule', ScheduleDrivers::class);
});
