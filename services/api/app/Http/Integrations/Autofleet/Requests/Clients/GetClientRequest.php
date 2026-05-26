<?php

namespace App\Http\Integrations\Autofleet\Requests\Clients;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetClientRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $id)
    {

    }

    public function resolveEndpoint(): string
    {
        return "v1/clients/{$this->id}";
    }
}
