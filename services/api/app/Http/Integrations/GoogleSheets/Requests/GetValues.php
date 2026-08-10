<?php

namespace App\Http\Integrations\GoogleSheets\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetValues extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $spreadsheetId,
        protected string $range,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/{$this->spreadsheetId}/values/" . rawurlencode($this->range);
    }
}
