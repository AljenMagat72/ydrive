<?php

namespace App\Http\Integrations\ChatwootPlatforms\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class SearchContacts extends Request
{

    protected Method $method = Method::GET;

    public function __construct(protected string $accountId, protected array $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/api/v1/accounts/{$this->accountId}/contacts/search";
    }

    public function defaultQuery(): array
    {
        return $this->data;
    }
}
