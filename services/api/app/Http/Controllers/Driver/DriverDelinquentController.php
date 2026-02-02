<?php

namespace App\Http\Controllers\Driver;
use App\Http\Controllers\Controller;

use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;

class DriverDelinquentController extends Controller
{
  public function __construct(
    protected DriverService $driverService,
  ) {

  }

  public function revert($id): JsonResponse
  {
    $driver = Driver::where([
      'id' => $id,
    ])->first();

    $this->driverService->removeFromDelinquents($driver);

    return response()->json([
      'success' => true,
    ]);
  }

  public function prevent($id): JsonResponse
  {
    $driver = Driver::where([
      'id' => $id,
    ])->first();

    if ($driver->is_delinquent) {
      $this->driverService->removeFromDelinquents($driver);
    }

    $driver->prevent_delinquency = true;
    $driver->save();

    return response()->json([
      'success' => true,
    ]);
  }
}
