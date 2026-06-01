<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RidePriceSnapshot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RidePriceSnapshotController extends Controller
{
  /**
   * List recent ride price snapshots (dev / internal tooling).
   *
   * @queryParam limit int optional max rows (default 2000, max 50000)
   */
  public function index(Request $request): JsonResponse
  {
    $limit = (int) $request->query('limit', 2000);
    $limit = min(max($limit, 1), 50000);

    $rows = RidePriceSnapshot::query()
      ->orderByDesc('id')
      ->limit($limit)
      ->get();

    return response()->json([
      'rows' => $rows,
      'count' => $rows->count(),
    ]);
  }
}
