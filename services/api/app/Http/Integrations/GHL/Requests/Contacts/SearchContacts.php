<?php

namespace App\Http\Integrations\GHL\Requests\Contacts;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class SearchContacts extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected array $query)
    {
    }

    public function resolveEndpoint(): string
    {
        return '/contacts/';
    }

    protected function defaultQuery(): array
    {
        return array_merge([
            'locationId' => config('services.ghl.location_id'),
        ], $this->query);
    }
}
