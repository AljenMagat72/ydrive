<?php

namespace App\Http\Integrations\ChatwootPlatforms\Requests\Contacts\Labels;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateLabels extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $accountId, protected string $contactId, protected array $labels)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/api/v1/accounts/{$this->accountId}/contacts/{$this->contactId}/labels";
    }

    protected function defaultBody()
    {
        return [
            'payload' => $this->labels
        ];
    }
}
