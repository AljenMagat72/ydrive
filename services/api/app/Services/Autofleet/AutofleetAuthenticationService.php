<?php

namespace App\Services\Autofleet;

use App\Http\Integrations\Autofleet\Requests\Login\RefreshTokenRequest;
use Cache;

class AutofleetAuthenticationService
{
    private string $accessTokenKey = 'autofleet:access_token';
    private string $refreshTokenKey = 'autofleet:refresh_token';
    private string $lockKey = 'autofleet:refresh';

    public function getValidAccessToken()
    {
        if (Cache::has($this->accessTokenKey)) {
            return Cache::get($this->accessTokenKey);
        }

        return $this->refreshToken();
    }

    public function refreshToken()
    {
        return Cache::withoutOverlapping($this->lockKey, function () {
            if (Cache::has($this->accessTokenKey)) {
                return Cache::get($this->accessTokenKey);
            }

            $refreshToken = Cache::get($this->refreshTokenKey);

            if (!$refreshToken) {
                throw new \Exception('No Refresh token found');
            }

            $request = new RefreshTokenRequest(Cache::get($this->refreshTokenKey));
            $response = $request->send();

            if ($response->failed()) {
                throw new \Exception('Failed to refresh: ' . $response->body());
            }

            $token = $response->json('token');
            $refreshToken = $response->json('refreshToken');

            Cache::put($this->accessTokenKey, $token);
            Cache::put($this->refreshTokenKey, $refreshToken);

            return $token;
        });
    }

    public function clearToken()
    {
        Cache::forget($this->accessTokenKey);
    }

    public function authenticate(string $refreshToken)
    {
        Cache::set($this->refreshTokenKey, $refreshToken);
        $this->refreshToken();
    }
}
