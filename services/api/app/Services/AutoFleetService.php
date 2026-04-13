<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AutoFleetService
{
  private const TOKEN_CACHE_KEY = 'autofleet_token';
  private const REFRESH_TOKEN_CACHE_KEY = 'autofleet_refresh_token';
  private const SERVICE_CACHE_PREFIX = 'autofleet_service:';

  private string $refreshToken;
  private ?string $token = null;
  private string $baseUrl;
  private string $fleetId;

  public function __construct()
  {
    $this->refreshToken = config('autofleet.refresh_token');
    $this->baseUrl = config('autofleet.end_point');
    $this->fleetId = config('autofleet.fleet_id');
  }

  private function getHttpClient()
  {
    $this->ensureTokenLoaded();

    if ($this->token === '') {
      $this->refreshToken();
    }

    return Http::withHeaders([
      'Authorization' => "Bearer $this->token",
    ])->baseUrl($this->baseUrl);
  }

  private function ensureTokenLoaded(): void
  {
    if ($this->token === null) {
      $this->token = Cache::get(self::TOKEN_CACHE_KEY, '');
    }
  }

  public function refreshToken(): void
  {
    $response = Http::post("$this->baseUrl/v1/login/refresh", [
      'refreshToken' => $this->refreshToken,
    ]);

    if ($response->ok()) {
      $data = $response->json();
      $this->token = $data['token'];
      $this->refreshToken = $data['refreshToken'];

      Cache::put(self::TOKEN_CACHE_KEY, $this->token);
    } else {
      throw new \Exception('Unable to refresh token: ' . $response->body(), $response->status());
    }
  }

  /**
   * Execute an API call with automatic token refresh on unauthorized response
   */
  private function makeAuthenticatedRequest(callable $requestCallback)
  {
    $response = $requestCallback($this->getHttpClient());

    if ($response->unauthorized()) {
      $this->refreshToken();
      $response = $requestCallback($this->getHttpClient());
    }

    return $response;
  }

  public function queryDrivers(array $params)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($params) {
      return $client->post('v1/drivers/query', $params);
    });

    return $response->json();
  }

  public function getRides(array $params)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($params) {
      return $client->get('v1/rides', $params);
    });

    return $response->json();
  }

  public function getServiceById(string $serviceId)
  {
    $cacheKey = self::SERVICE_CACHE_PREFIX . $serviceId;

    return Cache::remember($cacheKey, now()->addHours(6), function () use ($serviceId) {
      $response = $this->makeAuthenticatedRequest(function ($client) use ($serviceId) {
        return $client->get("v1/services/$serviceId");
      });

      return $response->json();
    });
  }

  public function updateDriver(string $driverId, array $params)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($driverId, $params) {
      return $client->patch("v1/drivers/$driverId", $params);
    });

    return $response->json();
  }

  public function updateRide(string $rideId, array $params)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($rideId, $params) {
      return $client->patch("v1/rides/$rideId", $params);
    });

    return $response->json();
  }

  public function queryClients(array $params)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($params) {
      return $client->post('v1/clients/query', $params);
    });

    return $response->json();
  }

  public function getDriverById(string $id)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($id) {
      return $client->get("v1/drivers/$id");
    });

    return $response->json();
  }

  public function getClientById(string $id)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($id) {
      return $client->get("v1/clients/$id");
    });

    return $response->json();
  }

  public function getAllDrivers(int $offset)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($offset) {
      return $client->get("v1/drivers", [
        "offset" => $offset,
      ]);
    });

    return $response->json();
  }

  public function getDriverByPhoneNumber(string $phoneNumber)
  {
    $query = $this->queryDrivers([
      'perPage' => 1,
      'page' => 1,
      'searchTerm' => $phoneNumber
    ]);

    $count = $query['count'];
    $rows = $query['rows'];

    if ($count === 0 || $rows[0]['phoneNumber'] !== $phoneNumber)
      return null;

    return $rows[0];
  }

  public function fixRateCashRide(array $ride)
  {
    $id = $ride['id'];
    $paymentMethod = $ride['payment']['paymentMethod']['id'];
    $priceAmount = $ride['priceAmount'];
    $businessModelId = $ride['businessModelId'];

    if ($businessModelId !== config('autofleet.business_models.sudbury'))
      return;

    if (!($paymentMethod === 'cash'))
      return;

    $cached = Cache::get("$id-cash-ride");

    if ($cached)
      return;

    Cache::put("$id-cash-ride", true);

    $actionId = rtrim(strtr(base64_encode(random_bytes(22)), '+/', '-_'), '=');

    $driverEarnings = $businessModelId === config('autofleet.business_models.medicine_hat') ? 0.8 : 0.75;

    $action = [
      'id' => $actionId,
      'description' => 'Fixed ride fare',
      'amount' => floor($priceAmount),
      'driverEarnings' => floor($priceAmount) * $driverEarnings,
      'action' => 'add',
      'chargeFor' => 'fixed',
      'type' => 'fixedPrice',
    ];

    $this->patchRideActions($id, [$action]);
  }

  public function getRide(string $rideId)
  {
    $response = $this->makeAuthenticatedRequest(function ($client) use ($rideId) {
      return $client->get("v1/rides/$rideId");
    });

    return $response->json();
  }

  public function patchRideActions(string $rideId, array $actions)
  {
    $this->makeAuthenticatedRequest(function ($client) use ($rideId, $actions) {
      return $client->patch("v1/rides/$rideId", [
        "pricingActions" => $actions
      ]);
    });
  }

  public function getVendorList()
  {
    $fleetId = $this->fleetId;

    $response = $this->makeAuthenticatedRequest(function ($client) use ($fleetId) {
      return $client->get("v1/vendors?fleetId=$fleetId");
    });

    return $response->json();
  }
}
