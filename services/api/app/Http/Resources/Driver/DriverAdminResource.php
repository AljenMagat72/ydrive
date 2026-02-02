<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class DriverAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        // group schedules by day of week
        $weekSchedule = $this->schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->starts_at)->format('l'); // Monday, Tuesday, etc.
        })->map(function ($daySchedules) {
            return $daySchedules->map(function ($s) {
                $start = Carbon::parse($s->starts_at)->format('g:ia');
                $end = Carbon::parse($s->ends_at)->format('g:ia');
                return [
                    'id' => $s->id,
                    "$start - $end"
                ];
            })->values();
        });

        // ensure all days exist
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            if (!isset($weekSchedule[$day])) {
                $weekSchedule[$day] = [];
            }
        }

        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'phoneNumber' => $this->phone_number,
            'city' => $this->city_id,
            'hasCurrentSchedule' => $this->has_current_schedule,
            'hasNextSchedule' => $this->has_next_schedule,
            'acceptanceRate' => $this->acceptance_rate,
            'acceptanceRateNeeded' => $this->acceptance_rate_needed,
            'minimumScheduledHours' => $this->minimum_scheduled_hours,
            'expiredOffers' => $this->expired_offers,
            'rejectedOffers' => $this->rejected_offers,
            'schedules' => $weekSchedule,
        ];
    }
}
