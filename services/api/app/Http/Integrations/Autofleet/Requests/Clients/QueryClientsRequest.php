<?php

namespace App\Http\Integrations\Autofleet\Requests\Clients;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class QueryClientsRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $searchTerm, protected int $page)
    {

    }

    public function resolveEndpoint(): string
    {
        return 'v1/clients/query';
    }

    public function defaultBody()
    {
        return [
            'page' => $this->page,
            'searchTerm' => $this->searchTerm,
        ];
    }
}
