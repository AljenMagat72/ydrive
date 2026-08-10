<?php

namespace App\Http\Integrations\GHL;

use App\Http\Integrations\GHL\Resources\ContactsResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class GHLApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return config('services.ghl.base_url', 'https://services.leadconnectorhq.com');
    }

    protected function defaultAuth(): Authenticator
    {
        return new TokenAuthenticator(config('services.ghl.api_key', 'mock-ghl-api-key'));
    }

    protected function defaultHeaders(): array
    {
        return [
            'Version' => config('services.ghl.api_version', '2021-07-28'),
            'Accept' => 'application/json',
        ];
    }

    public function contacts(): ContactsResource
    {
        return new ContactsResource($this);
    }
}
