<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AutoFleetService;
use Illuminate\Http\Request;
use Throwable;

class ClientController extends Controller
{
  public function __construct(protected AutoFleetService $autoFleetService)
  {
  }

  /**
   * @return array<string, mixed>
   */
  private function buildRidesQueryParams(string $clientId, array $validated): array
  {
    $params = ['clientId' => $clientId];
    if (array_key_exists('pageNumber', $validated) && $validated['pageNumber'] !== null) {
      $params['pageNumber'] = (int) $validated['pageNumber'];
    }
    return $params;
  }

  private function normalizePhoneForMatch(string $phone): string
  {
    return preg_replace('/\D+/', '', $phone) ?? '';
  }

  private function respondClientFound(array $row)
  {
    return response()->json([
      'id' => $row['id'] ?? null,
      'row' => $row,
    ]);
  }

  private function respondClientNotFound(string $reason)
  {
    return response()->json(['message' => $reason], 404);
  }

  private function queryClientsSafe(array $params)
  {
    try {
      return $this->autoFleetService->queryClients($params);
    } catch (Throwable $e) {
      return response()->json([
        'message' => 'AutoFleet clients query failed',
        'details' => $e->getMessage(),
      ], 502);
    }
  }

  private function searchByPhone(string $phone)
  {
    $needle = $this->normalizePhoneForMatch($phone);
    if ($needle === '') {
      return response()->json(['message' => 'Invalid phone'], 422);
    }

    $users = $this->autoFleetService->queryClients([
      'searchTerm' => $phone,
    ]);

    $rows = is_array($users['rows'] ?? null) ? $users['rows'] : [];
    foreach ($rows as $row) {
      if (!is_array($row)) {
        continue;
      }
      if (empty($row['phoneNumber'])) {
        continue;
      }
      $candidate = $this->normalizePhoneForMatch((string) $row['phoneNumber']);
      if ($candidate === '' || $candidate !== $needle) {
        continue;
      }
      return $this->respondClientFound($row);
    }

    return $this->respondClientNotFound('Client not found for given phone');
  }

  /**
   * @param  array<int, mixed>  $rows
   * @return array<int, array<string, mixed>>
   */
  private function filterRowsByExactEmail(array $rows, string $email): array
  {
    $matches = [];
    foreach ($rows as $row) {
      if (!is_array($row)) {
        continue;
      }
      $rowEmail = isset($row['email']) ? (string) $row['email'] : '';
      if ($rowEmail !== '' && strcasecmp($rowEmail, $email) === 0) {
        $matches[] = $row;
      }
    }

    return $matches;
  }

  private function rowHasVerifiedEmail(array $row): bool
  {
    return array_key_exists('isEmailVerified', $row) && $row['isEmailVerified'] === true;
  }

  /**
   * @param  array<int, array<string, mixed>>  $matches
   */
  private function respondEmailLookup(array $matches): \Illuminate\Http\JsonResponse
  {
    if (count($matches) === 0) {
      return $this->respondClientNotFound('Client not found for given email');
    }
    if (count($matches) === 1) {
      return $this->respondClientFound($matches[0]);
    }

    $verified = array_values(array_filter($matches, fn ($row) => is_array($row) && $this->rowHasVerifiedEmail($row)));
    if (count($verified) === 1) {
      return $this->respondClientFound($verified[0]);
    }

    return response()->json([
      'message' => 'Multiple clients matched the given email',
      'count' => count($matches),
    ], 409);
  }

  private function searchByEmail(string $email)
  {
    $clients = $this->queryClientsSafe([
      'searchTerm' => $email,
    ]);
    if ($clients instanceof \Illuminate\Http\JsonResponse) {
      return $clients;
    }

    $rows = is_array($clients['rows'] ?? null) ? $clients['rows'] : [];

    return $this->respondEmailLookup($this->filterRowsByExactEmail($rows, $email));
  }

  public function search(Request $request)
  {
    $data = $request->validate([
      'phone' => ['nullable', 'string'],
      'email' => ['nullable', 'string', 'email:rfc'],
      'name' => ['nullable', 'string'],
    ]);

    $phone = trim((string) ($data['phone'] ?? ''));
    $email = trim((string) ($data['email'] ?? ''));
    $name = trim((string) ($data['name'] ?? ''));

    $hasPhone = $phone !== '';
    $hasEmail = $email !== '';
    $hasName = $name !== '';

    if ($hasPhone) {
      return $this->searchByPhone($phone);
    }

    if ($hasEmail) {
      return $this->searchByEmail($email);
    }

    $message = !$hasName
      ? 'Provide at least one of: phone, email, name'
      : 'Name-only search is not supported; provide email or phone to disambiguate';

    return response()->json(['message' => $message], 422);
  }

  public function ridesById(string $id, Request $request)
  {
    $validated = $request->validate([
      'pageNumber' => ['nullable', 'integer', 'min:0'],
    ]);

    $params = $this->buildRidesQueryParams($id, $validated);

    try {
      $rides = $this->autoFleetService->getRides($params);
    } catch (Throwable $e) {
      return response()->json([
        'message' => 'AutoFleet rides request failed',
        'details' => $e->getMessage(),
      ], 502);
    }

    return response()->json($rides);
  }
}
