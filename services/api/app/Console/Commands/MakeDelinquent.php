<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Driver;
use App\Services\DriverService;

class MakeDelinquent extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'driver:make-delinquent {autofleet_id} {notify?}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Mark a driver as delinquent, optionally with a notification';

  public function __construct(protected DriverService $driverService)
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $id = $this->argument('autofleet_id');
    $notify = $this->argument('notify');

    $driver = Driver::where('autofleet_driver_id', $id)->first();

    if ($driver === null || $driver->is_delinquent)
      return Command::FAILURE;

    $this->driverService->addToDelinquents($driver);

    //TODO: handle notification
    if (!$notify)
      return;
  }
}
