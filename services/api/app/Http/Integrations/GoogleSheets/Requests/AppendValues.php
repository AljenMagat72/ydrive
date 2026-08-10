<?php

namespace App\Http\Integrations\GoogleSheets\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class AppendValues extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $spreadsheetId,
        protected string $range,
        protected array $values,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/{$this->spreadsheetId}/values/" . rawurlencode($this->range) . ':append';
    }

    protected function defaultQuery(): array
    {
        return [
            'valueInputOption' => 'USER_ENTERED',
            'insertDataOption' => 'INSERT_ROWS',
        ];
    }

    protected function defaultBody(): array
    {
        return [
            'values' => $this->values,
        ];
    }
}
