<?php

namespace App\Http\Integrations\Autofleet\Requests\Drivers;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

class UpdateDriver extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $id, protected array $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return "/v1/drivers/{$this->id}";
    }

    protected function defaultBody(): array
    {
        return $this->data;
    }
}
