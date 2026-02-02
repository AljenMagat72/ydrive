<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Services\VendorService;
=======
use App\Models\Driver;
use App\Models\VendorList;
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5
use Illuminate\Http\Request;


class VendorController extends Controller
{
<<<<<<< HEAD
    protected $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function all(Request $request)
    {
        try {
            $vendors = $this->vendorService->getAllVendors();
=======
    public function all(Request $request)
    {
        try {
            $vendors = VendorList::get();
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5

            return response()->json([
                'vendors' => $vendors,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage,
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $driverId = $request->get('driver_id');
        $vendorId = $request->get('vendor_id');

        try {
<<<<<<< HEAD
            $this->vendorService->update($driverId, $vendorId);
=======
            $noOppsId = VendorList::where('vendor_id', $vendorId)
                ->get()
                ->value('no_opps_id');

            Driver::where('id', $driverId)
                ->update([
                    'city_id' => $noOppsId,
                    'is_active' => false
                ]);
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
<<<<<<< HEAD
=======
                'error' => $th->getMessage(),
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5
            ], 500);
        }
    }

    public function get(Request $request)
    {
        try {
<<<<<<< HEAD
            $noOppsDrivers = $this->vendorService->getNoOppsDrivers();
=======
            $noOppsDrivers =  Driver::where('city_id', 'LIKE', '%NO OPPS%')
                ->orderByDesc('created_at')
                ->get();
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5

            return response()->json([
                'drivers' => $noOppsDrivers,
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage,
                'success' => false,
            ], 500);
        }
    }

    public function revert(Request $request)
    {
        $driverId = $request->get('driver_id');
        $noOppsId = $request->get('no_opps_id');

        try {
<<<<<<< HEAD
            $this->vendorService->revert($driverId, $noOppsId);
=======
            $vendorId = VendorList::where('no_opps_id', $noOppsId)
                ->get()
                ->value('vendor_id');

            Driver::where('id', $driverId)
                ->update([
                    'city_id' => $vendorId,
                    'is_active' => true
                ]);
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
<<<<<<< HEAD
=======
                'error' => $th->getMessage(),
>>>>>>> aab1ea2d5472ef05a4be17b39c2807651c2e17b5
            ], 500);
        }
    }
}
