<?php

namespace App\Http\Resources\Driver;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverDelinquentResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $currentPeriod = $this->currentDelinquentPeriod;
    $consecutiveWeeks = $currentPeriod ? floor(Carbon::parse($currentPeriod->started_at)->diffInWeeks(now())) : 0;

    return [
      'id' => $this->id,
      'firstName' => $this->first_name,
      'lastName' => $this->last_name,
      'phoneNumber' => $this->phone_number,
      'city' => $this->city_id,
      'consecutiveWeeks' => $consecutiveWeeks,
    ];
  }
}
