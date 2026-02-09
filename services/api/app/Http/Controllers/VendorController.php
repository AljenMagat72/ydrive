<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\VendorList;
use App\Services\AutoFleetService;
use Illuminate\Http\Request;


class VendorController extends Controller
{
    public function __construct(protected AutoFleetService $autofleetService)
    {
        $this->autofleetService = $autofleetService;
    }

    public function all(Request $request)
    {
        try {
            $vendors = VendorList::get();

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
        $vendorName = $request->get('vendor_id');
        $autofleetDriverId = $request->get('autofleet_driver_id');

        try {
            $driver = Driver::findOrFail($driverId);

            $driverFromAutofleet = $this->autofleetService->getDriverById($autofleetDriverId);
            $labelValue = $driverFromAutofleet['labels'][0]['value'] ?? null;

            $vendor = null;

            if (!empty($driverFromAutofleet['vendorId'])) {
                $vendor = VendorList::where('vendor_id', $driverFromAutofleet['vendorId'])->first();
            }

            if ($labelValue && $labelValue !== $vendorName) {
                $driver->city_id = $labelValue;
                $vendor = VendorList::where('vendor_name', $labelValue)->first();
            }

            if (!$vendor) {
                $vendor = VendorList::where('vendor_name', $vendorName)->first();
            }

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor could not be resolved',
                ], 422);
            }

            $driver->vendor_list_id = $vendor->id;
            $driver->is_delinquent = true;
            $driver->save();

            $this->autofleetService->updateDriver(
                $autofleetDriverId,
                ['vendorId' => $vendor->no_opps_id]
            );

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function get(Request $request)
    {
        try {
            $noOppsDrivers =  Driver::where('vendor_list_id', '!=', null)
                ->orderByDesc('created_at')
                ->get();

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
        $vendorName = $request->get('vendor_name');
        $autofleetDriverId = $request->get('autofleet_driver_id');

        try {
            $driverFromAutofleet = $this->autofleetService->getDriverById($autofleetDriverId);

            $vendorId = VendorList::where('no_opps_id', $driverFromAutofleet['vendorId'])->value('vendor_id');

            $driver = Driver::where('id', $driverId)->first();
            $driver->is_delinquent = false;
            $driver->save();

            $this->autofleetService->updateDriver($autofleetDriverId, ['vendorId' => $vendorId]);

            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
