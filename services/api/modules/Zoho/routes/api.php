<?php
use Illuminate\Support\Facades\Route;
use Modules\Zoho\Services\ZohoService;
use Modules\Zoho\Http\Controllers\ZohoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/driver-details/{id}', [ZohoController::class, 'show']);
    Route::get('/driver-documents/{zohoId}', function ($zohoId, \Modules\Zoho\Services\ZohoService $zoho) {
    return $zoho->getAttachments($zohoId);
    });

    Route::post('/driver/request-banking-update', [ZohoController::class, 'requestBankingUpdate']);
});

Route::get('/debug-fields', function (ZohoService $zoho) {
    $data = $zoho->getFieldsMetadata();
    
    if(!isset($data['fields'])) return response()->json(['error' => 'Could not fetch fields', 'raw' => $data]);

    return collect($data['fields'])->map(fn($f) => [
        'label' => $f['field_label'],
        'api_name' => $f['api_name'],
        'type' => $f['data_type']
    ]);
});

Route::get('/debug-attachments/{id}', function ($id, ZohoService $zoho) {
    // This will now work because we added getAttachments to the service!
    return $zoho->getAttachments($id);
});

Route::get('/view-attachment/{contactId}/{fileId}', function ($contactId, $fileId, \Modules\Zoho\Services\ZohoService $zoho) {
    // Try downloading as a Field Upload first
    $file = $zoho->downloadFileField($contactId, $fileId);
    
    if (!$file) {
        return response()->json(['error' => 'File ID not valid for this field type'], 404);
    }

    return response($file['content'])
        ->header('Content-Type', $file['type']);
});

?>