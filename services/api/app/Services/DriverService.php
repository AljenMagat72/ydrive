<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\SMSCode;

class DriverService
{
  protected $autofleet;

  public function __construct(AutoFleetService $autofleet)
  {
    $this->autofleet = $autofleet;
  }

  public function findOrCreateDriverWithToken(string $phoneNumber): ?array
  {
    $driver = Driver::where('phone_number', $phoneNumber)->first()
      ?? $this->findOrCreateDriverByPhoneNumber($phoneNumber);

    if (!$driver) {
      return null;
    }

    $this->syncDriverCity($driver);

    $token = $this->generateAuthVerifyToken($driver);
    $this->generateSMSCode($driver);

    return [
      'driver' => $driver,
      'token'  => $token->plainTextToken,
    ];
  }


  public function findOrCreateDriverByPhoneNumber(string $phoneNumber)
  {
    $autofleetDriver = $this->autofleet->getDriverByPhoneNumber($phoneNumber);

    if (!$autofleetDriver || $autofleetDriver['vendor'] === null || $this->isValidCanadianPhoneNumber($phoneNumber)) {
      return null;
    }

    $driver = $this->updateOrCreateAutofleetDriver($autofleetDriver);

    return $driver;
  }

  public function findOrCreateDriverById(string $id)
  {
    $autofleetDriver = $this->autofleet->getDriverById($id);

    if (!$autofleetDriver || $autofleetDriver['vendor'] === null) {
      return null;
    }

    $driver = $this->updateOrCreateAutofleetDriver($autofleetDriver);

    return $driver;
  }

  public function updateOrCreateAutofleetDriver(array $autofleetDriver)
  {
    return Driver::updateOrCreate(
      [
        'autofleet_driver_id' => $autofleetDriver['id']
      ],
      [
        'first_name' => $autofleetDriver['firstName'],
        'last_name' => $autofleetDriver['lastName'],
        'avatar' => $autofleetDriver['avatar'],
        'city_id' => $autofleetDriver['labels'][0]['value'] ?? "",
        'phone_number' => $autofleetDriver['phoneNumber'],
        'is_active' => !empty($vendorName),
      ]
    );
  }

  public function generateSMSCode(Driver $driver)
  {
    SMSCode::create([
      'driver_id' => $driver->id,
    ]);
  }

  public function verifySMSCode(Driver $driver, string $code)
  {
    //TODO: setup a service to delete old sms codes:
    SMSCode::where('created_at', '<', now()->subMinutes(5))->delete();

    $smsCode = SMSCode::where([
      'driver_id' => $driver->id,
      'code' => $code
    ])
      ->where('created_at', '>=', now()->subMinutes(5))
      ->first();

    if (!$smsCode) {
      return false;
    }

    $smsCode->delete();
    return true;
  }

  public function resendSMSCode(Driver $driver)
  {
    $this->generateSMSCode($driver);
  }

  public function verifyAndAuthenticateDriver(Driver $driver, string $code)
  {
    if (!$this->verifySMSCode($driver, $code)) {
      return false;
    }

    return [
      'token' => $this->generateAuthToken($driver)->plainTextToken
    ];
  }

  public function generateAuthVerifyToken(Driver $driver)
  {
    return $driver->createToken($driver->id, ['auth.driver.verify'], now()->addHour());
  }

  public function generateAuthToken(Driver $driver)
  {
    return $driver->createToken($driver->id, ['driver.portal'], now()->addDay());
  }

  public function addToDelinquents(Driver $driver)
  {
    if ($driver->is_delinquent)
      return;

    $noOppsId = VendorList::where('vendor_id', $driver->vendor_id)->value('no_opps_id');

    $driver->update([
      'is_delinquent' => true,
    ]);

    Log::info("Adding $driver->first_name ($driver->autofleet_driver_id) to delinquents");

    if ($noOppsId === null)
      return;
  }

  public function removeFromDelinquents(Driver $driver)
  {
    if (!$driver->is_delinquent)
      return;

    $driver->update([
      'is_delinquent' => false,
    ]);

    $vendorId = $driver->vendor->vendor_id;
  }

  function isValidCanadianPhoneNumber(string $phone): bool
  {
    return preg_match(
      '/^(?:\+1\s?)?(?:\(?[2-9][0-9]{2}\)?[\s.-]?)?[2-9][0-9]{2}[\s.-]?[0-9]{4}$/',
      $phone
    ) === 1;
  }

  private function syncDriverCity(mixed $driver): void
  {
    $city = $this->autofleet->getDriverById($driver['autofleet_driver_id']);

    $cityLabel = $city['labels'][0]['value'] ?? null;

    if (!$cityLabel) {
      return;
    }

    Driver::where('autofleet_driver_id', $driver['autofleet_driver_id'])
      ->update(['city_id' => trim($cityLabel)]);
  }
}
