<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverScheduleResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    return [
      'startsAt' => $this->starts_at->format('Y-m-d\TH:i:s'),
      'endsAt' => $this->ends_at->format('Y-m-d\TH:i:s'),
      'id' => $this->uuid,
      'driverId' => $this->driver->uuid
    ];
  }
}
