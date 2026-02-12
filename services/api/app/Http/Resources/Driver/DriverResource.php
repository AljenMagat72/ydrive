<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'firstName' => $this->first_name,
      'lastName' => $this->last_name,
      'avatar' => $this->avatar,
      'phoneNumber' => $this->phone_number,
      'cityId' => $this->city_id,
      'minimumScheduledHours' => $this->minimum_scheduled_hours,
      'acceptanceRate' => $this->acceptance_rate,
      'acceptanceRateNeeded' => $this->acceptance_rate_needed,
      'zoho_id' => $this->zoho_id,
    ];
  }
}
