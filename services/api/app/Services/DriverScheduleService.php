<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\DriverSchedule;
use Illuminate\Database\Eloquent\Collection;

class DriverScheduleService
{
  public function add($driverId, $startsAt, $endsAt): DriverSchedule
  {
    $existing = DriverSchedule::where('driver_id', $driverId)
      ->whereDate('starts_at', Carbon::parse($startsAt)->toDateString())
      ->first();

    if ($existing) {
      $updateData = [];

      if ($existing->starts_at->format('Y-m-d\TH:i:s') !== $startsAt) {
        $updateData['starts_at'] = $startsAt;
      }

      if ($existing->ends_at->format('Y-m-d\TH:i:s') !== $endsAt) {
        $updateData['ends_at'] = $endsAt;
      }

      if (!empty($updateData)) {
        $existing->update($updateData);
      }

      return $existing;
    }

    return DriverSchedule::create([
      'driver_id' => $driverId,
      'starts_at' => $startsAt,
      'ends_at' => $endsAt,
    ]);
  }

  public function addWeek(int $driveId, $array, $startDate): void
  {
    //TODO: this could be sped up with a bulk operation, but it's fast enough
    for ($i = 0; $i < 7; $i++) {
      $dailySchedule = $array[$i];

      $currentDate = Carbon::parse($startDate);
      $currentDate->addDays($i);

      if ($dailySchedule) {
        $this->add($driveId, $dailySchedule['startsAt'], $dailySchedule['endsAt']);
      } else {
        $this->delete($driveId, $currentDate->toDateString());
      }
    }
  }

  public function delete(int $driverId, string $date): bool
  {
    $deletedRows = DriverSchedule::where('driver_id', $driverId)
      ->whereDate('starts_at', $date)
      ->delete();

    return $deletedRows > 0;
  }

  public function weekly(int $driverId, string $date): array
  {
    $startDate = Carbon::parse($date)->startOfWeek(0);
    $endDate = Carbon::parse($date)->endOfWeek(6);


    // Start and end of the week
    $startDate = Carbon::parse($date)->startOfWeek(); // Monday
    $endDate = Carbon::parse($date)->endOfWeek(); // Sunday

    $result = [];

    while ($startDate->lte($endDate)) {
      $dayName = $startDate->format('l'); // Monday, Tuesday, etc.
      $currentDate = $startDate->toDateString();

      // Fetch schedule for this driver and date
      $schedules = DriverSchedule::where('driver_id', $driverId)
        ->whereDate('starts_at', $currentDate)
        ->get();

      // Map schedules to desired format
      $result[$dayName] = $schedules->map(function ($schedule) {
        return [
          'id' => $schedule->id,
          'time' => $schedule->starts_at->format('g:ia') . ' - ' . $schedule->ends_at->format('g:ia'),
          'created_at' => $schedule->created_at->toDateTimeString(),
        ];
      })->toArray();

      // Ensure the key exists even if empty
      if (empty($result[$dayName])) {
        $result[$dayName] = [];
      }

      $startDate->addDay();
    }

    return $result;
  }

  public function daily(string $date, string $city): Collection
  {
    $dateString = Carbon::parse($date)->toDateString();

    return DriverSchedule::with('driver')
      ->where(function ($query) use ($dateString) {
        $query->whereDate('starts_at', $dateString);
      })
      ->when($city !== 'All', function ($query) use ($city) {
        $query->whereHas('driver', function ($query) use ($city) {
          $query->where('city_id', 'LIKE', "%$city%");
        });
      })
      ->orderBy('starts_at')
      ->get();
  }

  public function range(string $startDate, string $endDate, string $city): Collection
  {
    $startDateString = Carbon::parse($startDate)->startOfDay();
    $endDateString   = Carbon::parse($endDate)->endOfDay();

    return DriverSchedule::with('driver')
      ->whereBetween('starts_at', [$startDateString, $endDateString])
      ->when($city !== 'All', function ($query) use ($city) {
        $query->whereHas('driver', function ($query) use ($city) {
          $query->where('city_id', 'LIKE', "%{$city}%");
        });
      })
      ->orderBy('starts_at')
      ->get();
  }

  public function withIncompleteSchedules() {}
}
