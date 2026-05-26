<?php

namespace App\Http\Integrations\Zoho\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

class OAuthTokenRequest extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        protected string $refreshToken,
        protected string $clientId,
        protected string $clientSecret,
        protected string $grantType,
    ) {

    }

    public function resolveEndpoint(): string
    {
        return 'https://accounts.zoho.com/oauth/v2/token';
    }

    public function defaultBody()
    {
        return [
            'refresh_token' => $this->refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => $this->grantType,
        ];
    }
}
