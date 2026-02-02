<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\AutoFleetService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
  public function __construct(protected AutoFleetService $autoFleetService)
  {
  }

  public function rides(string $id, Request $request)
  {
    return $this->autoFleetService->getRides([
      'clientId' => $id,
      'pageNumber' => $request->input('page'),
    ]);
  }

  public function find(Request $request)
  {
    $phone = $request->input('phone');

    $users = $this->autoFleetService->queryClients([
      'page' => 1,
      'searchTerm' => $phone,
    ]);

    $user = $users['rows'][0] ?? null;
    if (
      $user &&
      !empty($user['phoneNumber']) &&
      strtolower($user['phoneNumber']) === strtolower($phone)
    ) {
      return $user;
    }

    return response()->json(null);
  }
}
