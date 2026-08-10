<?php

namespace App\Http\Integrations\Airtable;

use App\Http\Integrations\Airtable\Resources\RecordsResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class AirtableApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        $baseId = config('services.airtable.base_id', 'appMockBaseId');

        return 'https://api.airtable.com/v0/' . $baseId;
    }

    protected function defaultAuth(): Authenticator
    {
        return new TokenAuthenticator(config('services.airtable.api_key', 'mock-airtable-api-key'));
    }

    public function records(string $table): RecordsResource
    {
        return new RecordsResource($this, $table);
    }
}
