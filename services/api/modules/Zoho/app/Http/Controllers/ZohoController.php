<?php

namespace Modules\Zoho\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Zoho\Services\ZohoService;
use Illuminate\Support\Facades\Mail;

class ZohoController extends Controller
{
    protected $zoho;

    public function __construct(ZohoService $zoho)
    {
        $this->zoho = $zoho;
    }

    public function index(Request $request)
    {
        // Get current page from URL, default to 1
        $page = $request->get('page', 1);

        try {
            // Fetch live data from Zoho
            $results = $this->zoho->getDrivers($page);

            return view('zoho::index', [
                'drivers'     => $results['data'] ?? [],
                'info'        => $results['info'] ?? [],
                'currentPage' => $page
            ]);
        } catch (\Exception $e) {
            return back()->withError("Could not fetch drivers: " . $e->getMessage());
        }
    }

    public function profile()
    {
        $user = auth()->user();

        if (!$user->zoho_id) {
            return "This account is not linked to a Zoho Driver.";
        }

        // You can either show the data saved in the DB:
        return view('zoho::profile', ['driver' => $user]);

        // OR fetch the LATEST live data from Zoho using their ID:
        // $liveData = $this->zoho->getDriverById($user->zoho_id);
        // return view('zoho::profile', ['driver' => $liveData]);
    }
    public function show($id)
    {
        try {
            $result = $this->zoho->getContactById($id);

            if (isset($result['data'][0])) {
                $z = $result['data'][0];

                return response()->json([
                    'success' => true,
                    'data' => [
                            'Full_Name'          => $z['Full_Name'] ?? '---',
                            'Phone'              => $z['Phone'] ?? '---',
                            'Date_of_Birth'      => $z['Date_of_Birth'] ?? '---',
                            'Make'               => $z['Make'] ?? '---',
                            'Model'              => $z['Model'] ?? '---',
                            'Year'               => $z['Year'] ?? '---',
                            'Bank_Name'          => $z['Bank_Name'] ?? '---',
                            'Bank_Account'       => $z['Account'] ?? '---',   // Maps Zoho "Account" to Interface "Bank_Account"
                            'HSTGST'             => $z['HST_GST'] ?? '---',   // Maps Zoho "HST_GST" to Interface "HSTGST"
                            'License_Class'      => $z['License_Class'] ?? '---',
                            'License_Exp'        => $z['License_Exp'] ?? '---',
                            'City_License_Exp'   => $z['City_License_Exp'] ?? '---',
                            'Criminal_Check_Exp' => $z['Criminal_Check_Exp'] ?? '---',
                            'Abstract_Exp'       => $z['Abstract_Exp'] ?? '---',
                            'Insurance_Exp'      => $z['Insurance_Exp'] ?? '---',
                            'Registration_Exp'   => $z['Registration_Exp'] ?? '---',
                            'Safety_Exp'         => $z['Safety_Exp'] ?? '---',
                        ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Driver not found in Zoho'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
}