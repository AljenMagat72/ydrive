<?php

namespace App\Http\Integrations\Autofleet\Resources;

use App\Http\Integrations\Autofleet\Requests\Rides\GetRideRequest;
use Saloon\Http\BaseResource;

class RidesResource extends BaseResource
{
    public function get(string $rideId)
    {
        return $this->connector->send(new GetRideRequest($rideId));
    }
}
