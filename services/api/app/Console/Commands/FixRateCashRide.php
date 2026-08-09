<?php

namespace App\Console\Commands;

use App\Services\AutoFleetService;
use Illuminate\Console\Command;

class FixRateCashRide extends Command
{

  public function __construct(
    protected AutoFleetService $autoFleetService,
  ) {
    parent::__construct();
  }

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'auto-fleet:fix-rate-cash-ride {rideId}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Fix a cash ride rate';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $rideId = $this->argument('rideId');
    
    $ride = $this->autoFleetService->getRide($rideId);

    $this->autoFleetService->fixRateCashRide($ride);
  }
}
