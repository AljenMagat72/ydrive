<?php

namespace App\Console\Commands;

use App\Models\Driver;
use App\Services\AutoFleetService;
use App\Services\DriverService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
  protected $signature = 'app:move-drivers';

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
    $previous = Driver::where('is_delinquent', true)
      ->where('is_active', true)
      ->get();
    $collection = Driver::InCompleteSchedules();

    $previousById = $previous->keyBy('id');
    $currentById = $collection->keyBy(fn($item) => $item->driver->id);

    $addtions = $currentById->diffKeys($previousById);
    $removals = $previousById->diffKeys($currentById);

    foreach ($addtions as $item) {
      $this->driverService->addToDelinquents($item->driver);
    }

    foreach ($removals as $item) {
      $this->driverService->removeFromDelinquents($item);
    }
  }
}
