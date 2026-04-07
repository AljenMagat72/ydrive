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

  private function normalizePhoneForMatch(string $phone): string
  {
    // AutoFleet phone values may be stored without "+" or formatting.
    // Compare only digits to avoid false negatives (e.g. "+1 (555) 123-4567" vs "15551234567").
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

  /**
   * @param  array<int, array<string, mixed>>  $matches
   */
  private function respondEmailLookup(array $matches): \Illuminate\Http\JsonResponse
  {
    if (count($matches) === 0) {
      return $this->respondClientNotFound('Client not found for given email');
    }
    if (count($matches) > 1) {
      return response()->json([
        'message' => 'Multiple clients matched the given email',
        'count' => count($matches),
      ], 409);
    }

    return $this->respondClientFound($matches[0]);
  }

  private function searchByEmail(string $email, string $name = '')
  {
    $searchTerm = $name !== '' ? ($name . ' ' . $email) : $email;

    $clients = $this->queryClientsSafe([
      'searchTerm' => $searchTerm,
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

    // Prefer exact match on phone.
    if ($hasPhone) {
      return $this->searchByPhone($phone);
    }

    // Email-only or name+email lookup. We only accept an exact email match to avoid fuzzy mis-association.
    if ($hasEmail) {
      return $this->searchByEmail($email, $hasName ? $name : '');
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

    $params = ['clientId' => $id];
    if (array_key_exists('pageNumber', $validated) && $validated['pageNumber'] !== null) {
      $params['pageNumber'] = (int) $validated['pageNumber'];
    }

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
