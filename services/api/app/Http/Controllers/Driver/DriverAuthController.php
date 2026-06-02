<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DriverLoginRequest;
use App\Http\Requests\Auth\DriverVerifyRequest;
use App\Http\Resources\Driver\DriverResource;
use App\Models\Driver;
use App\Notifications\LoginCodeNotification;
use App\Services\Driver\DriverService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverAuthController extends Controller
{

    public function __construct(
        protected DriverService $driver,
    ) {
    }

    public function login(DriverLoginRequest $request): JsonResponse
    {
        $driver = $this->driver->findOrCreateDriverByPhoneNumber($request->input('phoneNumber'));

        $otp = $driver->createOneTimePassword();
        $driver->notify(new LoginCodeNotification($otp->password));

        return response()->json([], 200);
    }

    public function verify(DriverVerifyRequest $request): JsonResponse
    {
        $driver = Driver::wherePhoneNumber($request->input('phoneNumber'))->firstOrFail();
        $result = $driver->consumeOneTimePassword($request->input('code'));

        if (!$result->isOk()) {
            return response()->json([
                'success' => false,
                'message' => $result->validationMessage(),
            ], 422);
        }

        Auth::guard('driver')->login($driver);
        $token = $driver->createToken($driver->id, ['driver.portal']);

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'user' => new DriverResource($driver),
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json();
    }
}
