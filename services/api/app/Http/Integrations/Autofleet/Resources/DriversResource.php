<?php

namespace App\Http\Integrations\Autofleet\Resources;

use App\Http\Integrations\Autofleet\Requests\Drivers\UpdateDriver;
use Saloon\Http\BaseResource;

class DriversResource extends BaseResource
{
    public function update(string $driverId, array $data)
    {
        return $this->connector->send(new UpdateDriver($driverId, $data));
    }
}
