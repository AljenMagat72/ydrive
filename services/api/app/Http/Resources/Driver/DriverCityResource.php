<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverCityResource extends JsonResource
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
      'phoneNumber' => $this->phone_number,
      'cityId' => $this->city_id,
    ];
  }
}
