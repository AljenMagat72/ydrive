<?php

namespace App\Http\Integrations\Autofleet\Requests\Login;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasJsonBody;

class RefreshTokenRequest extends SoloRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected string $refreshToken)
    {
    }

    public function resolveEndpoint(): string
    {
        return config('autofleet.end_point').'/v1/login/refresh';
    }

    public function defaultBody()
    {
        return [
            'refreshToken' => $this->refreshToken,
        ];
    }
}
