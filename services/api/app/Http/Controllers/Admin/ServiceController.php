<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AutoFleetService;
use Throwable;

class ServiceController extends Controller
{
  public function __construct(protected AutoFleetService $autoFleetService)
  {
  }

  public function show(string $id)
  {
    try {
      $svc = $this->autoFleetService->getServiceById($id);
    } catch (Throwable $e) {
      return response()->json([
        'message' => 'AutoFleet service request failed',
        'details' => $e->getMessage(),
      ], 502);
    }

    return response()->json($svc);
  }
}

