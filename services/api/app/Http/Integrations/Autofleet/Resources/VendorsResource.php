<?php

namespace App\Http\Integrations\Autofleet\Resources;

use App\Http\Integrations\Autofleet\Requests\Vendors\GetVendorsRequest;
use Saloon\Http\BaseResource;

class VendorsResource extends BaseResource
{
    public function get(string $fleetId)
    {
        return $this->connector->send(new GetVendorsRequest($fleetId));
    }
}
