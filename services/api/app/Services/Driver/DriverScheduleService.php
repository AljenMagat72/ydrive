<?php

namespace App\Services\Driver;

use Carbon\Carbon;
use App\Models\DriverSchedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class DriverScheduleService
{
    public function add($driverId, $startsAt, $endsAt): DriverSchedule
    {
        return DriverSchedule::create([
            'driver_id' => $driverId,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'uuid' => Str::uuid(),
        ]);
    }

    public function delete(DriverSchedule $driverSchedule)
    {
        $driverSchedule->deleteOrFail();
    }

    public function weekly(int $driverId, string $date)
    {
        $startDate = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
        $endDate = Carbon::parse($date)->endOfWeek(Carbon::SUNDAY);

        return DriverSchedule::where('driver_id', $driverId)
            ->whereBetween('starts_at', [$startDate, $endDate])
            ->get();
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
}
