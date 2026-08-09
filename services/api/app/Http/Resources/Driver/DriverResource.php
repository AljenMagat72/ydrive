<?php

namespace App\Http\Resources\Driver;

use App\Settings\DriverSettings;
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
      'id' => $this->uuid,
      'firstName' => $this->first_name,
      'lastName' => $this->last_name,
      'avatar' => $this->avatar,
      'minimumScheduledHours' => $this->minimum_scheduled_hours ?? app(DriverSettings::class)->minimum_scheduled_hours,
      'minimumAcceptanceRate' => $this->minimum_acceptance_rate ?? app(DriverSettings::class)->minimum_acceptance_rate
    ];
  }
}
