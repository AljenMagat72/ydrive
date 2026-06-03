<?php

namespace App\Services\Driver;

use App\Models\Driver;
use App\Models\VendorList;
use App\Services\AutoFleetService;
use ErrorException;

class DriverService
{
    public function __construct(protected AutoFleetService $autofleet, protected DriverDelinquentService $driverDelinquent)
    {
    }

    public function findOrCreateDriverByPhoneNumber(string $phoneNumber)
    {
        $autofleetPhoneNumber = ltrim($phoneNumber, '+');

        $autofleetDriver = $this->autofleet->getDriverByPhoneNumber($autofleetPhoneNumber);

        return $this->updateOrCreateAutofleetDriver($autofleetDriver);
    }

    public function findOrCreateDriverById(string $id)
    {
        $autofleetDriver = $this->autofleet->getDriverById($id);

        return $this->updateOrCreateAutofleetDriver($autofleetDriver);
    }

    public function updateOrCreateAutofleetDriver(array $autofleetDriver)
    {
        $city = $this->getDriverCity($autofleetDriver['id']);
        $vendor = data_get($autofleetDriver, 'vendor');
        $vendorId = null;
        $isDelinquent = false;

        if($vendor) {
            $vendorStatus = $this->driverDelinquent->resolve($vendor['id']);
            $vendorId = $vendorStatus['id'];
            $isDelinquent = $vendorStatus['isDelinquent'];
        }

        return Driver::updateOrCreate(
            [
                'autofleet_driver_id' => $autofleetDriver['id']
            ],
            [
                'first_name' => $autofleetDriver['firstName'],
                'last_name' => $autofleetDriver['lastName'],
                'avatar' => $autofleetDriver['avatar'],
                'city_id' => $city,
                'phone_number' => "+" . $autofleetDriver['phoneNumber'],
                'is_active' => (bool)data_get($autofleetDriver, 'vendor.name'),
                'vendor_list_id' => $vendorId,
                'is_delinquent' => $isDelinquent,
            ]
        );
    }

    private function getDriverCity(string $uuid)
    {
        $city = $this->autofleet->getDriverById($uuid);

        $cityLabel = $city['labels'][0]['value'] ?? null;

        if (!$cityLabel) {
            throw new \Error();
        }

        return trim($cityLabel);
    }
}
