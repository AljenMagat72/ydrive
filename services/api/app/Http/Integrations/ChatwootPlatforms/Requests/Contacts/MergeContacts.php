<?php

namespace App\Http\Integrations\ChatwootPlatforms\Requests\Contacts;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class MergeContacts extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $accountId, protected string $baseContactId, protected string $mergeeContactId)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/api/v1/accounts/{$this->accountId}/actions/contact_merge";
    }

    protected function defaultBody()
    {
        return [
            'base_contact_id' => $this->baseContactId,
            'mergee_contact_id' => $this->mergeeContactId,
        ];
    }
}
