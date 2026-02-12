<?php

use Illuminate\Support\Facades\Route;
use Modules\Zoho\Http\Controllers\ZohoController;

Route::get('/drivers', [ZohoController::class, 'index'])->name('zoho.drivers');