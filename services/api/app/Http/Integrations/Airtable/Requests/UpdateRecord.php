<?php

namespace App\Http\Integrations\Airtable\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateRecord extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        protected string $table,
        protected string $recordId,
        protected array $fields,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/' . rawurlencode($this->table) . '/' . $this->recordId;
    }

    protected function defaultBody(): array
    {
        return [
            'fields' => $this->fields,
            'typecast' => true,
        ];
    }
}
