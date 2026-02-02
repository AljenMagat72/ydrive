<?php

namespace App\Services;

use App\Repositories\VendorRepository;

class VendorService
{
    protected $vendorRepository;

    public function __construct(VendorRepository $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    public function getAllVendors()
    {
        return $this->vendorRepository->getAllVendors();
    }

    public function update($driverId, $vendorId)
    {
        return $this->vendorRepository->update($driverId, $vendorId);
    }

    public function getNoOppsDrivers()
    {
        return $this->vendorRepository->getNoOppsDrivers();
    }

    public function revert($driverId, $noOppsId)
    {
        return $this->vendorRepository->revert($driverId, $noOppsId);
    }
}
