<?php

use Illuminate\Support\Facades\Route;
use Modules\Zoho\Http\Controllers\ZohoController;
use Modules\Zoho\Services\ZohoService;

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/driver-details', [ZohoController::class, 'show']);
    
    Route::get('/driver-documents', [ZohoController::class, 'getDocuments']);
    
    Route::get('/view-attachment/{fileId}', [ZohoController::class, 'viewAttachment']);

    Route::post('/driver/request-banking-update', [ZohoController::class, 'requestBankingUpdate']);

});