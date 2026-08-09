<?php

namespace App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Notes;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class CreateNote extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $accountId, protected string $contactId, protected string $note)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/api/v1/accounts/{$this->accountId}/contacts/{$this->contactId}/notes";
    }

    protected function defaultBody()
    {
        return [
            'content' => $this->note,
        ];
    }
}
