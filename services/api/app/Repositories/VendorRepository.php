<?php

namespace App\Repositories;

use App\Models\VendorList;
use App\Models\Driver;

class VendorRepository
{
    protected $vendors;
    protected $drivers;

    public function __construct(VendorList $vendors, Driver $drivers)
    {
        $this->vendors = $vendors;
        $this->drivers = $drivers;
    }

    public function getAllVendors()
    {
        return $this->vendors->get();
    }

    public function update($driverId, $vendorId)
    {

        $noOppsId = $this->vendors->where('vendor_id', $vendorId)
            ->get()
            ->value('no_opps_id');

        return $this->drivers->where('id', $driverId)
            ->update(['city_id' => $noOppsId]);
    }

    public function getNoOppsDrivers()
    {
        return $this->drivers->where('city_id', 'LIKE', '%NO OPPS%')
            ->orderByDesc('created_at')
            ->get();
    }

    public function revert($driverId, $noOppsId)
    {

        $vendorId = $this->vendors->where('no_opps_id', $noOppsId)
            ->get()
            ->value('vendor_id');

        return $this->drivers->where('id', $driverId)
            ->update(['city_id' => $vendorId]);
    }
}
