<?php

namespace App\Http\Controllers;

use App\Services\AutoFleetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnchantController extends Controller
{
  public function __construct(protected AutoFleetService $autoFleetService)
  {
  }

  public function customerView(Request $request)
  {
    $secretKey = 'zECoEe8wjQuzxk0FQdws8f2hjRrhgplN';

    $payload = $request->getContent();
    $signature = $request->header('Enchant-Signature');

    $calculated = hash_hmac('sha256', $payload, $secretKey);

    if (!hash_equals($calculated, $signature)) {
      abort(401, 'Invalid signature');
    }

    $firstName = $request->input('first_name');
    $lastName = $request->input('last_name');

    $contacts = collect($request->input('contacts'));

    $email = optional($contacts->firstWhere('type', 'email'))['value'] ?? null;
    $phone = ltrim(optional($contacts->firstWhere('type', 'phone'))['value'] ?? '', '+') ?: null;

    $user = $this->findUser($firstName, $lastName, $email, $phone);

    if ($user === null) {
      return response('No client found', 200)->header('Content-Type', 'text/html');
    }

    $rides = collect($this->autoFleetService->getRides([
      'clientId' => $user['id'],
    ]));

    $activeRide = $rides->whereIn('state', ['matching', 'active', 'dispatched'])->first();
    $latestRide = $rides->whereIn('state', ['completed', 'rejected', 'canceled', 'failed'])->first();
    $upcomingRide = $rides->firstWhere('state', 'pending');

    return view('enchant.customer', [
      'activeRide' => $activeRide,
      'latestRide' => $latestRide,
      'upcomingRide' => $upcomingRide,
    ]);
  }

  protected function findUser($firstName, $lastName, $email = null, $phone = null)
  {
    $service = $this->autoFleetService;

    if ($phone) {
      $users = $service->queryClients([
        'page' => 1,
        'searchTerm' => $phone,
      ]);

      $user = $users['rows'][0] ?? null;
      if ($user && !empty($user['phoneNumber']) && strtolower($user['phoneNumber']) === strtolower($phone)) {
        return $user;
      }
    }

    if ($email) {
      $users = $service->queryClients([
        'page' => 1,
        'searchTerm' => $email,
      ]);

      $user = $users['rows'][0] ?? null;
      if ($user && !empty($user['email']) && strtolower($user['email']) === strtolower($email)) {
        return $user;
      }
    }

    if ($firstName && $lastName) {
      $users = $service->queryClients([
        'page' => 1,
        'searchTerm' => "{$firstName} {$lastName}",
      ]);

      $user = $users['rows'][0] ?? null;
      if (
        $user && !empty($user['firstName']) && !empty($user['lastName'])
        && strtolower($user['firstName']) === strtolower($firstName)
        && strtolower($user['lastName']) === strtolower($lastName)
      ) {
        return $user;
      }
    }

    return null;
  }
}
