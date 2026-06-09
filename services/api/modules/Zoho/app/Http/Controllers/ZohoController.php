<?php

namespace Modules\Zoho\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Zoho\Services\ZohoService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\BankingUpdateRequest;
use App\Mail\HSTGSTUpdateRequest;

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

            $cachedData = Cache::remember("driver_profile_{$zohoId}", now()->addHours(24), function () use ($zohoId) {
            $result = $this->zoho->getContactById($zohoId);
            $z = $result['data'][0] ?? null;

            if (!$z) return null;

            return [
                'id'                 => $zohoId,
                'Full_Name'          => $z['Full_Name'] ?? null,
                'City'               => $z['City'] ?? null,
                'Phone'              => $z['Phone'] ?? null,
                'Date_of_Birth'      => $z['Date_of_Birth'] ?? null,
                'Make'               => $z['Make'] ?? null,
                'Model'              => $z['Model'] ?? null,
                'Year'               => $z['Year'] ?? null,
                'Bank_Name'          => $z['Bank_Name'] ?? null,
                'Bank_Account'       => $z['Account'] ?? null,
                'Transit'            => $z['Transit'] ?? null,
                'Institution'        => $z['Institution'] ?? null,
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
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}

public function updateDocument(Request $request): JsonResponse
{
    try {
        $zohoId = $this->getZohoId($request);
        $file = $request->file('document');
        $fieldName = $request->input('document_type');

        if (!$zohoId || !$file || !$fieldName) {
            return response()->json(['success' => false, 'message' => 'Missing data.'], 400);
        }

        $targetFile = is_array($file) ? $file[0] : $file;

        $result = $this->zoho->uploadToFileField($zohoId, $fieldName, $targetFile);

        if ($result['success']) {
            Cache::forget("driver_profile_{$zohoId}");
            $this->zoho->updateContact($zohoId, ['Tag' => [['name' => 'needs update']]]);
            
            return response()->json([
                'success' => true, 
                'message' => "Successfully updated $fieldName."
            ]);
        }

        return response()->json(['success' => false, 'message' => $result['message']], 400);

    } catch (\Exception $e) {
        Log::error("Zoho Controller 500: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'success' => false, 
            'message' => 'Server Error: ' . $e->getMessage()
        ], 500);
    }
}

    public function viewAttachment(Request $request, string $fileId)
    {
        $contactId = $this->getZohoId($request);
        if (!$contactId) return response()->json(['error' => 'No Zoho ID'], 403);

        $file = $this->zoho->downloadFileField($contactId, $fileId);
        if (!$file || empty($file['content'])) return response()->json(['error' => 'Not found'], 404);

        return response($file['content'], 200)
            ->header('Content-Type', $file['type'] ?? 'image/jpeg')
            ->header('Content-Disposition', 'inline; filename="attachment"');
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $zohoId = $this->getZohoId($request);

        $validated = $request->validate([
            'Bank_Name'    => 'nullable|string',
            'Bank_Account' => 'nullable|string',
            'Institution'  => 'nullable|string',
            'Transit'       => 'nullable|string',
            'HST_GST'       => 'nullable|string',
        ]);

        $mapping = [
            'Bank_Name'     => 'TBU_Bank_Name',
            'Bank_Account'  => 'TBU_Bank_Account',
            'Institution'   => 'TBU_Institution',
            'Transit'       => 'TBU_Transit',
            'HST_GST'       => 'TBU_HST_GST',
        ];

        $zohoData = [];

        foreach ($validated as $key => $value) {
            if ($request->has($key)) {
                $zohoData[$mapping[$key]] = $value;
            }
        }

        if (empty($zohoData)) {
            return response()->json([
            'success' => false, 
            'message' => 'No data to update',
            'received' => $request->all(),
            'expected_one_of' => array_keys($mapping)
        ], 400);
        }

        Cache::forget("driver_profile_{$zohoId}");
        $result = $this->zoho->updateContact($zohoId, $zohoData);
        try {
            $adminEmails = config('zoho.zoho_admin_emails');
            $name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email;
            
            if ($request->has('HST_GST')) {
                Mail::to($adminEmails)->send(new HSTGSTUpdateRequest($validated, $name));
            } else {
                Mail::to($adminEmails)->send(new BankingUpdateRequest($validated, $name));
            }
        } catch (\Exception $e) {
            Log::error("Profile Update Email Failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Details submitted for admin review.',
            'data' => $result
        ]);
    }
}