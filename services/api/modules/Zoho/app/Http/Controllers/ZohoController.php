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
                return response()->json([
                    'success' => true,
                    'data'    => $result['data'][0] 
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