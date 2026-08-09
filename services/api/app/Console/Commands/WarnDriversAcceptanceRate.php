<?php

namespace App\Console\Commands;

use App\Models\Driver;
use Illuminate\Console\Command;

class WarnDriversAcceptanceRate extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'driver:warn-acceptance-rate {city_id}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send the warning to drivers that they failing their acceptance rate';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $cityId = $this->argument('city_id');
    $collection = Driver::NotMeetingAcceptanceRate($cityId);

    $collection->each(function ($driver) {
      // TODO: send warning
      $this->info("Warned Driver {$driver->id} (City: {$driver->city_id})");
    });
  }
}
