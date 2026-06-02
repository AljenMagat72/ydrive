<?php

namespace App\Console\Commands;

use App\Services\AutoFleetService;
use App\Services\Driver\DriverService;
use Illuminate\Console\Command;

class AddDriver extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:add-driver {phone_number}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Add a driver from autofleet';

  public function __construct(protected DriverService $driverService)
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $this->driverService->findOrCreateDriverByPhoneNumber($this->argument('phone_number'));
  }
}
