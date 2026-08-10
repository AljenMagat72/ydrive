<?php

namespace App\Http\Integrations\Airtable\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateRecords extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $table,
        protected array $records,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/' . rawurlencode($this->table);
    }

    protected function defaultBody(): array
    {
        return [
            'records' => $this->records,
            'typecast' => true,
        ];
    }
}
