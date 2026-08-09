<?php

namespace App\Console\Commands;

use App\Models\Driver;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class WarnDrivers extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:warn-drivers';

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
    $collection = Driver::InCompleteSchedules();

    $collection->each(function(Driver $driver) {
      //TODO: send warning
    });
  }
}