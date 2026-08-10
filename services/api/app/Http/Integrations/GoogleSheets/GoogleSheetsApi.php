<?php

namespace App\Http\Integrations\GoogleSheets;

use App\Http\Integrations\GoogleSheets\Resources\ValuesResource;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class GoogleSheetsApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function resolveBaseUrl(): string
    {
        return config('services.google_sheets.base_url', 'https://sheets.googleapis.com/v4/spreadsheets');
    }

    protected function defaultAuth(): Authenticator
    {
        return new TokenAuthenticator(config('services.google_sheets.access_token', 'mock-google-sheets-token'));
    }

    public function values(string $spreadsheetId): ValuesResource
    {
        return new ValuesResource($this, $spreadsheetId);
    }
}
