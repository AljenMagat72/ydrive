<?php

use Illuminate\Support\Facades\Route;
use Modules\Zoho\Http\Controllers\ZohoController;
use Modules\Zoho\Services\ZohoService;
use Modules\Zoho\Http\Controllers\AdminZohoController;

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/

//driver routes
Route::middleware(['auth:sanctum', 'abilities:driver.portal'])->group(function () {
    Route::get('/driver-details', [ZohoController::class, 'show']);
    Route::get('/driver-documents', [ZohoController::class, 'getDocuments']);
    Route::get('/view-attachment/{fileId}', [ZohoController::class, 'viewAttachment']);
    Route::post('/zoho/update-document', [ZohoController::class, 'updateDocument']);
});

//admin Routes
Route::middleware(['auth:sanctum', 'abilities:admin.portal'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/driver-details/{zohoId}', [AdminZohoController::class, 'show']);
        Route::get('/view-attachment/{zohoId}/{fileId}', [AdminZohoController::class, 'viewAttachment']);
        Route::post('/driver-documents-zip/{zohoId}', [AdminZohoController::class, 'downloadZip']);
    });
});
