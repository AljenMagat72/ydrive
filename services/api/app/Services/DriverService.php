<?php

namespace App\Services;

use App\Models\Driver;
use App\Models\DriverDelinquentPeriod;
use App\Models\SMSCode;
use Illuminate\Support\Carbon;

class DriverService
{
  protected $autofleet;
  protected $driverRepository;

  public function __construct(AutoFleetService $autofleet)
  {
    $this->autofleet = $autofleet;
  }

  public function findOrCreateDriverWithToken(string $phoneNumber): ?array
  {
    $driver =  Driver::where('phone_number', $phoneNumber)->first()
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
    $autofleetDriver = $this->autofleet->getDriverById($driver->autofleet_driver_id);
    $originalVendorId = $autofleetDriver['vendorId'];

    $driver->update([
      'is_delinquent' => true,
      'original_vendor_id' => $originalVendorId,
    ]);

    DriverDelinquentPeriod::create([
      'driver_id' => $driver->id,
      'started_at' => Carbon::now(),
    ]);

    $cityKey = strtolower(str_replace(' ', '_', $driver->city_id));

    // Get the no_opp_vendor ID from config
    //$noOppVendorId = config("autofleet.business_models.{$cityKey}.no_opp_vendor") ?? null;

    /*if ($noOppVendorId) {
      $this->autofleet->updateDriver($driver->autofleet_driver_id, [
        'vendorId' => $noOppVendorId
      ]);
    }*/
  }

  public function removeFromDelinquents(Driver $driver)
  {
    //$autofleetDriver = $this->autofleet->getDriverById($driver->autofleet_driver_id);
    $originvalVendorId = $driver->original_vendor_id;

    $driver->update([
      'original_vendor_id' => null,
      'is_delinquent' => false,
    ]);

    $currentPeriod = DriverDelinquentPeriod::where('driver_id', $driver->id)
      ->whereNull('ended_at')
      ->first();

    if ($currentPeriod) {
      $currentPeriod->ended_at = Carbon::now();
      $currentPeriod->save();
    }

    /**
     * $this->autofleet->updateDriver($driver->autofleet_driver_id, [
     *  "vendorId" => $originvalVendorId
     * ])
     */
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
      ->update(['city_id' =>  trim($cityLabel)]);
  }
}
