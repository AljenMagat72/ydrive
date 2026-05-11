<?php

namespace Modules\Zoho\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Zoho\Services\ZohoService;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class AdminZohoController extends Controller
{
    protected ZohoService $zoho;

    public function __construct(ZohoService $zoho)
    {
        $this->zoho = $zoho;
    }

    /**
     * Admin: Fetch any driver's details by Zoho ID
     */
    public function show(string $zohoId): JsonResponse
    {
        try {
            $cachedData = Cache::remember("admin_zoho_driver_{$zohoId}", now()->addHours(24), function () use ($zohoId) {
                $result = $this->zoho->getContactById($zohoId);
                $z = (isset($result['data']) && count($result['data']) > 0) ? $result['data'][0] : null;

                if (!$z) {
                    return null;
                }

                return [
                    'id'                  => $zohoId,
                    'Full_Name'           => $z['Full_Name'] ?? null,
                    'Criminal_Vulnerable' => $z['Criminal_Vulnerable'] ?? null,
                    'Drivers_Abstract'    => $z['Drivers_Abstract'] ?? null,
                    'Insurance_Photo'     => $z['Insurance_Photo'] ?? null,
                    'Vehicle_Ownership'   => $z['Vehicle_Ownership'] ?? null,
                    'Vehicle_Safety'      => $z['Vehicle_Safety'] ?? null,
                    'Drivers_License'     => $z['Drivers_License'] ?? null,
                    'City_License_Permit' => $z['City_License_Permit'] ?? null,
                    'Car_Photo'           => $z['Car_Photo'] ?? null,
                    
                    //expiry
                    'License_Exp'         => $z['License_Exp'] ?? null,
                    'Insurance_Exp'       => $z['Insurance_Exp'] ?? null,
                    'City_License_Exp'    => $z['City_License_Exp'] ?? null,
                    'Registration_Exp'    => $z['Registration_Exp'] ?? null,
                    'Criminal_Check_Exp'  => $z['Criminal_Check_Exp'] ?? null,
                    'Abstract_Exp'        => $z['Abstract_Exp'] ?? null,
                    'Safety_Exp'          => $z['Safety_Exp'] ?? null,
                ];
            });

            if (!$cachedData) {
                return response()->json(['success' => false, 'message' => 'Driver not found in Zoho'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $cachedData
            ]);

        } catch (\Exception $e) {
            Log::error("Zoho Show Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function viewAttachment($zohoId, $fileId)
    {
        $fileData = $this->zoho->downloadFileField($zohoId, $fileId);
        if (!$fileData) abort(404);

        return response($fileData['content'])
            ->header('Content-Type', $fileData['type'])
            ->header('Content-Disposition', 'attachment; filename="' . $fileData['name'] . '"');
    }

    public function downloadZip(Request $request, $zohoId)
    {
        if (!class_exists('ZipArchive')) {
            Log::error("PHP ZipArchive extension is not installed on this server.");
            return response()->json(['error' => 'Server missing Zip library'], 500);
        }

        $fileIds = $request->input('file_ids', []);
        if (empty($fileIds)) return response()->json(['error' => 'No files selected'], 400);

        $zip = new ZipArchive();
        $fileName = "driver_{$zohoId}_docs.zip";
        $tempPath = storage_path("app/public/{$fileName}");
        if (!file_exists(storage_path('app/public'))) {
            mkdir(storage_path('app/public'), 0755, true);
        }

        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($fileIds as $id) {
                try {
                    $fileData = $this->zoho->downloadFileField($zohoId, $id);
                        if ($fileData && !empty($fileData['content'])) {
                            $zip->addFromString($fileData['name'], $fileData['content']);
                        }
                } catch (\Exception $e) {
                    Log::warning("Failed to add file {$id} to zip: " . $e->getMessage());
                }
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Could not open zip file for writing'], 500);
        }

        if (file_exists($tempPath)) {
            return response()->download($tempPath)->deleteFileAfterSend(true);
        }

        return response()->json(['error' => 'Zip creation failed'], 500);
    }
}