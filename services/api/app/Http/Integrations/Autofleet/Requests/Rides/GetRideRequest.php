<?php

namespace App\Http\Integrations\Autofleet\Requests\Rides;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetRideRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $rideId)
    {
    }

    public function resolveEndpoint(): string
    {
        return "v1/rides/{$this->rideId}";
    }
}
