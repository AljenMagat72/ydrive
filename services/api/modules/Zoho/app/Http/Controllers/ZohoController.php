<?php

namespace Modules\Zoho\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Modules\Zoho\Services\ZohoService;
use Illuminate\Support\Facades\DB;

class ZohoController extends Controller
{
    protected ZohoService $zoho;

    public function __construct(ZohoService $zoho)
    {
        $this->zoho = $zoho;
    }

    /**
     * Helper to get Zoho ID from the drivers table
     */
    private function getZohoId(Request $request)
    {
    return $request->user()->zoho_id;
    }

    public function show(Request $request): JsonResponse
    {
        try {
            $zohoId = $this->getZohoId($request);

            if (!$zohoId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No Zoho profile linked to this account.'
                ], 403);
            }

            $result = $this->zoho->getContactById($zohoId);
            $z = $result['data'][0] ?? null;

            if (!$z) {
                return response()->json(['success' => false, 'message' => 'Driver not found in Zoho'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id'                 => $zohoId,
                    'Full_Name'          => $z['Full_Name'] ?? null,
                    'Phone'              => $z['Phone'] ?? null,
                    'Date_of_Birth'      => $z['Date_of_Birth'] ?? null,
                    'Make'               => $z['Make'] ?? null,
                    'Model'              => $z['Model'] ?? null,
                    'Year'               => $z['Year'] ?? null,
                    'Bank_Name'          => $z['Bank_Name'] ?? null,
                    'Bank_Account'       => $z['Account'] ?? null,
                    'HSTGST'             => $z['HST_GST'] ?? null,
                    'License_Class'      => $z['License_Class'] ?? null,
                    'License_Exp'        => $z['License_Exp'] ?? null,
                    'City_License_Exp'   => $z['City_License_Exp'] ?? null,
                    'Criminal_Check_Exp' => $z['Criminal_Check_Exp'] ?? null,
                    'Abstract_Exp'       => $z['Abstract_Exp'] ?? null,
                    'Insurance_Exp'      => $z['Insurance_Exp'] ?? null,
                    'Registration_Exp'   => $z['Registration_Exp'] ?? null,
                    'Safety_Exp'         => $z['Safety_Exp'] ?? null,

                    // Document File IDs
                    'Vehicle_Safety'     => $z['Vehicle_Safety'] ?? null,
                    'Insurance_Photo'    => $z['Insurance_Photo'] ?? null,
                    'Drivers_License'    => $z['Drivers_License'] ?? null,
                    'City_License_Permit'=> $z['City_License_Permit'] ?? null,
                    'Car_Photo'          => $z['Car_Photo'] ?? null,
                    'Vehicle_Ownership'  => $z['Vehicle_Ownership'] ?? null,
                    'Drivers_Abstract'   => $z['Drivers_Abstract'] ?? null,
                    'Criminal_Vulnerable'=> $z['Criminal_Vulnerable'] ?? null,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function viewAttachment(Request $request, string $fileId)
    {
        $contactId = $this->getZohoId($request);

        if (!$contactId) {
            return response()->json(['error' => 'No Zoho ID linked to user'], 403);
        }

        $file = $this->zoho->downloadFileField($contactId, $fileId);
        
        if (!$file || empty($file['content'])) {
            return response()->json([
                'error' => 'File content is empty or not found',
                'file_id' => $fileId
            ], 404);
        }

        return response($file['content'], 200)
            ->header('Content-Type', $file['type'] ?? 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Content-Disposition', 'inline; filename="attachment"');
    }

    public function getDocuments(Request $request)
    {
        $zohoId = $this->getZohoId($request);

        if (!$zohoId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        return $this->zoho->getAttachments($zohoId);
    }
}