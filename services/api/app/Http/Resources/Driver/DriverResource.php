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

      //zoho expiry
      'License_Exp'        => $this->license_expiry, 
      'Insurance_Exp'      => $this->insurance_expiry,
      'City_License_Exp'   => $this->city_license_expiry,
      'Registration_Exp'   => $this->registration_expiry,
      'Criminal_Check_Exp' => $this->criminal_expiry,
      'Abstract_Exp'       => $this->abstract_expiry,
      'Safety_Exp'         => $this->safety_expiry,
    ];
  }
}
