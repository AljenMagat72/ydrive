<?php

namespace App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetLabels extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected string $accountId, protected string $contactId)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/api/v1/accounts/{$this->accountId}/contacts/{$this->contactId}/labels";
    }
}
