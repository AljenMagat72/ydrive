<?php

namespace App\Http\Integrations\ChatwootPlatforms;

use App\Http\Integrations\ChatwootPlatforms\Resources\Contacts\ContactsResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\HeaderAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class ChatwootPlatformsApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return config('services.chatwoot.base_url');
    }

    protected function defaultAuth(): Authenticator
    {
        return new HeaderAuthenticator(config('services.chatwoot.api_key'), 'api_access_token');
    }

    protected function defaultHeaders(): array
    {
        return [];
    }

    protected function defaultConfig(): array
    {
        return [];
    }

    public function contacts(string $accountId)
    {
        return new ContactsResource($this, $accountId);
    }
}
