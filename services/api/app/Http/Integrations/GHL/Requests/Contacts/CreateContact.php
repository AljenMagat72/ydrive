<?php

namespace App\Http\Integrations\GHL\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateContact extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected array $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/contacts/';
    }

    protected function defaultBody(): array
    {
        return array_merge([
            'locationId' => config('services.ghl.location_id'),
        ], $this->data);
    }
}
