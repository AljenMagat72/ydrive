<?php

namespace App\Http\Integrations\Zoho;

use App\Http\Integrations\Zoho\Requests\OAuthTokenRequest;
use App\Http\Integrations\Zoho\Resources\Records\RecordsResource;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Saloon\Http\Auth\TokenAuthenticator;
use Illuminate\Support\Facades\Cache;
use Exception;

class ZohoApi extends Connector
{
    use AcceptsJson, AlwaysThrowOnErrors;

    protected string $accessTokenKey = 'zoho:access-token';
    protected string $lockKey = 'zoho:refresh';

    public function resolveBaseUrl(): string
    {
        return 'https://www.zohoapis.com/crm/v8';
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->getValidAccessToken());
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        if (!$exception instanceof RequestException || $exception->getResponse()->status() !== 401) {
            return false;
        }

        Cache::forget($this->accessTokenKey);

        $this->authenticate(new TokenAuthenticator($this->getValidAccessToken()));

        return true;
    }

    protected function getValidAccessToken(): string
    {
        return Cache::withoutOverlapping($this->lockKey, function () {
            if (Cache::has($this->accessTokenKey)) {
                return Cache::get($this->accessTokenKey);
            }

            $refreshToken = config('services.zoho.refresh_token');

            $request = new OAuthTokenRequest(
                $refreshToken,
                config('services.zoho.client_id'),
                config('services.zoho.client_secret'),
                'refresh_token'
            );

            $response = $request->send();

            if ($response->failed()) {
                throw new Exception("Zoho Token Refresh Failed: " . $response->body());
            }

            $accessToken = $response->json('access_token');

            Cache::put($this->accessTokenKey, $accessToken, 50 * 60);

            return $accessToken;
        });
    }

    public function records(string $api)
    {
        return new RecordsResource($this, $api);
    }
}
