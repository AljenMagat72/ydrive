<?php

namespace App\Console\Commands;

use App\Models\Driver;
use Illuminate\Console\Command;

class WarnDriversMinimumHours extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'driver:warn-minimum-hours {city_id}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Send the warning to drivers that they need to update their schedule';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $cityId = $this->argument('city_id');
    $collection = Driver::IncompleteSchedules($cityId);

    $collection->each(function ($driver) {
      // TODO: send warning
      $this->info("Warned Driver {$driver->id} (City: {$driver->city_id})");
    });
  }
}
