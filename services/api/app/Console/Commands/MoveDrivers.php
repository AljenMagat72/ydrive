<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Services\DriverService;
use App\Jobs\ConfirmScheduledHours;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MoveDrivers extends Command
{

  private DriverService $driverService;

  public function __construct(DriverService $driverService)
  {
    parent::__construct();
    $this->driverService = $driverService;
  }

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'driver:move-drivers {city_id}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Resets/Move drivers to the their respective vendor lists';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $cityId = $this->argument('city_id');

    $previous = Driver::where('is_delinquent', true)
      ->where('is_active', true)
      ->when($cityId, fn($q) => $q->where('city_id', $cityId))
      ->get();

    $incompleteSchedules = Driver::IncompleteSchedules($cityId);
    $belowAcceptanceRate = Driver::NotMeetingAcceptanceRate($cityId);

    $failingBoth = $incompleteSchedules->intersect($belowAcceptanceRate);

    $onlyFailingHours = $incompleteSchedules->diff($belowAcceptanceRate);

    $previousById = $previous->keyBy('id');
    $failingBothById = $failingBoth->keyBy('id');

    $additions = $failingBothById->diffKeys($previousById);

    $removals = $previousById->diffKeys($failingBothById);

    foreach ($additions as $driver) {
      $this->driverService->addToDelinquents($driver);
    }

    foreach ($removals as $driver) {
      $this->driverService->removeFromDelinquents($driver);
      //TODO: send congrats message
    }

    foreach ($onlyFailingHours as $driver) {
      ConfirmScheduledHours::dispatch($driver->id, $this->driverService)
        ->delay(now()->addHours(9));
      //TODO: move this to some descriptive value
    }
  }
}