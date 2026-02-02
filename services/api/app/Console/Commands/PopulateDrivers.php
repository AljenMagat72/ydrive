<?php

namespace App\Console\Commands;

use App\Services\AutoFleetService;
use App\Services\DriverService;
use Illuminate\Console\Command;

class PopulateDrivers extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:populate-drivers';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Populates all the drivers from autofleet';

  protected AutoFleetService $autofleetService;
  protected DriverService $driverService;

  public function __construct(AutoFleetService $autofleetService, DriverService $driverService) {
    parent::__construct();

    $this->autofleetService = $autofleetService;
    $this->driverService = $driverService;
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $offset = 0;

    do {
      $drivers = $this->autofleetService->getAllDrivers($offset);

      foreach($drivers as $driver) {
        $this->driverService->updateOrCreateAutofleetDriver($driver);
      }

      $offset += 200;
    } while(count($drivers) === 200);
  }
}
