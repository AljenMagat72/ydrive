<?php

use Inertia\Inertia;

Route::get('/login', fn() => Inertia::render('auth/Login'))->name('driver.login');

Route::middleware(['auth:driver'])->group(function () {
    Route::get('/', fn() => Inertia::render('Dashboard'))->name('driver.dashboard');
    Route::get('/schedule', fn() => Inertia::render('Schedule'))->name('driver.schedule');
    Route::get('/schedule/city', fn() => Inertia::render('ScheduleCity'))->name('driver.schedule.city');
});
