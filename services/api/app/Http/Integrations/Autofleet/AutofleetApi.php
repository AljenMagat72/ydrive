<?php

namespace App\Http\Integrations\Autofleet;

use App\Http\Integrations\Autofleet\Resources\ClientsResource;
use App\Http\Integrations\Autofleet\Resources\RidesResource;

use App\Http\Integrations\Autofleet\Resources\DriversResource;
use App\Http\Integrations\Autofleet\Resources\VendorsResource;
use App\Services\Autofleet\AutofleetAuthenticationService;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class AutofleetApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    public function __construct(protected AutofleetAuthenticationService $authenticationService) {

    }

    public function resolveBaseUrl(): string
    {
        return config('autofleet.end_point');
    }

    protected function defaultAuth(): \Saloon\Contracts\Authenticator|null
    {
        return new TokenAuthenticator($this->authenticationService->getValidAccessToken());
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->middleware()->onResponse(function (Response $response) {
            if ($response->status() !== 401) {
                return $response;
            }

            $this->authenticationService->clearToken();
            $this->authenticate(new TokenAuthenticator($this->authenticationService->getValidAccessToken()));

            return $this->send($response->getPendingRequest()->getRequest());
        });
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (!$exception instanceof RequestException || $exception->getResponse()->status() !== 401) {
            return false;
        }

        $this->authenticationService->clearToken();

        $this->authenticate(new TokenAuthenticator($this->authenticationService->getValidAccessToken()));

        return true;
    }

    public function clients()
    {
        return new ClientsResource($this);
    }

    public function rides()
    {
        return new RidesResource($this);
    public function drivers()
    {
        return new DriversResource($this);
    }

    public function vendors()
    {
        return new VendorsResource($this);
    }
}
