<?php

namespace App\Services\Driver;

use App\Http\Integrations\Autofleet\AutofleetApi;
use App\Models\Driver;
use App\Models\VendorList;
use App\Services\Autofleet\AutofleetVendorService;
use ErrorException;
use Illuminate\Support\Facades\Log;

class DriverDelinquentService
{
    public function __construct(
        protected AutofleetVendorService $autofleetVendorService,
        protected AutofleetApi $autofleetApi,
    )
    {

    }

    public function flag(Driver $driver)
    {
        if ($driver->is_delinquent) {
            throw new ErrorException();
        }

        $noOppsId = $driver->vendor->no_opps_id;

        if ($noOppsId === null) {
            throw new ErrorException();
        }

        $this->autofleetApi->drivers()->update($driver->autofleet_driver_id, [
            'vendorId' => $noOppsId,
        ]);

        $driver->update([
            'is_delinquent' => true,
        ]);
    }

    public function unflag(Driver $driver)
    {
        if (!$driver->is_delinquent) {
            throw new ErrorException();
        }

        $driver->update([
            'is_delinquent' => false,
        ]);

        $vendorId = $driver->vendor->vendor_id;

        $this->autofleetApi->drivers()->update($driver->autofleet_driver_id, [
            'vendorId' => $vendorId,
        ]);
    }

    public function resolve(string $autofleetVendorId): array
    {
        $vendor = $this->autofleetVendorService->findOrSyncVendor($autofleetVendorId);

        return [
            'id' => $vendor->id,
            'isDelinquent' => $vendor->no_opps_id === $autofleetVendorId,
        ];
    }
}
