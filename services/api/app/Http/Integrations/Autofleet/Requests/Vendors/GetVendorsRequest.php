<?php

namespace App\Http\Integrations\Autofleet\Requests\Vendors;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetVendorsRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $fleetId)
    {

    }

    public function resolveEndpoint(): string
    {
        return '/v1/vendors';
    }

    public function defaultQuery(): array
    {
        return [
            'fleetId' => $this->fleetId,
        ];
    }
}
