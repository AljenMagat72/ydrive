<?php

namespace App\Http\Integrations\Airtable\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class ListRecords extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        protected string $table,
        protected array $query = [],
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/' . rawurlencode($this->table);
    }

    protected function defaultQuery(): array
    {
        return $this->query;
    }
}
