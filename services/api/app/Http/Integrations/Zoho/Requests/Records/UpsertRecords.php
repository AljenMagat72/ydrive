<?php

namespace App\Http\Integrations\Zoho\Requests\Records;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpsertRecords extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $api,
        protected array $data,
        protected array $duplicateCheckFields,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "{$this->api}/upsert";
    }

    protected function defaultBody(): array
    {
        return [
            'data' => $this->data,
            'duplicate_check_fields' => $this->duplicateCheckFields,
        ];
    }
}
