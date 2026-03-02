<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DriverLoginRequest;
use App\Http\Requests\Auth\DriverVerifyRequest;
use App\Http\Resources\Driver\DriverResource;
use App\Models\Admin;
use App\Models\Driver;
use App\Services\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverAuthController extends Controller
{

  public function __construct(
    protected DriverService $driver,
  ) {}

  /**
   * login as a autofleet driver
   */
  public function login(DriverLoginRequest $request): JsonResponse
  {

    try {
      $auth = $this->driver->findOrCreateDriverWithToken($request->input('phoneNumber'));

      return response()->json([
        'success' => true,
        'token' => $auth['token']
      ], 200);
    } catch (\Throwable $th) {
      throw $th;
    }
  }

  /**
   * verify sms code
   */
  public function verify(DriverVerifyRequest $request): JsonResponse
  {
    $auth = $this->driver->verifyAndAuthenticateDriver($request->user(), $request->input('code'));

    if (!$auth) {
      return response()->json([
        'success' => false,
      ], 200);
    }

    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'success' => true,
      'token' => $auth['token'],
      'user' => new DriverResource($request->user()),
    ], 200);
  }

  public function me(Request $request): JsonResponse
  {
    if ($request->user()->role === 'admin') {
      return response()->json([
        'success' => true,
        'user' => [
          'id' => $request->user()->id,
          'name' => $request->user()->name,
          'email' => $request->user()->email,
          'role' => $request->user()->role,
        ]
      ]);
    } else {
      return response()->json([
        'success' => true,
        'user' => new DriverResource($request->user()),
      ]);
    }
  }

  public function read(Driver $driver) {


    return response()->json([
        'success' => true,
        'user' => new DriverResource($driver),
      ]);
  }

  public function resend(Request $request): JsonResponse
  {
    $this->driver->resendSMSCode($request->user());

    return response()->json([
      'success' => true,
    ]);
  }

  public function logout(Request $request): JsonResponse
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'success' => true,
    ]);
  }
}
