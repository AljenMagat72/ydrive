<?php

namespace App\Jobs;

use App\Services\Driver\DriverService;
use Illuminate\Contracts\Broadcasting\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use Illuminate\Support\Carbon;

use App\Models\Driver;

class ConfirmScheduledHours implements ShouldQueue, ShouldBeUnique
{
  use Queueable;

  public function __construct(
    public int $driverId,
    public DriverService $driverService
  ) {
  }

  public function uniqueId(): string
  {
    return $this->driverId;
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $nextWeekStart = Carbon::now()->addWeek()->startOfWeek();
    $nextWeekEnd = (clone $nextWeekStart)->endOfWeek();

    $driver = Driver::where('id', $this->driverId)->first();

    if (!$driver) {
      return;
    }

    $underHours = Driver::underHoursForRange($nextWeekStart, $nextWeekEnd)
      ->where('id', $this->driverId)
      ->exists();

    if ($driver->is_delinquent && !$underHours) {
      $this->driverService->removeFromDelinquents($driver);
      //TODO: send congrats on removal
    } elseif (!$driver->is_delinquent && $underHours) {
      $this->driverService->addToDelinquents($driver);
    }
  }
}
