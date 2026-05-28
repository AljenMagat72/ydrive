<?php

namespace App\Http\Resources\Driver;

use App\Enums\Role;
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
        $isAdmin = $request->user()->role == Role::ADMIN;

        return [
            'id' => $this->id,
            'cityId' => $this->city_id,
            ...($isAdmin ? [
                'firstName' => $this->first_name,
                'lastName' => $this->last_name,
                'phoneNumber' => $this->phone_number,
            ] : [
                'firstName' => 'Driver',
                'lastName' => ' ',
            ]),
        ];
    }
}
