<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    /**
     * Csrf
     * @return JsonResponse
     */
    public function csrf()
    {
        return response()->json(['message' => 'CSRF cookie set']);
    }

    /**
     * Login User
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        $token = $user->createToken('auth_token', ['admin.portal'])->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Handle user request
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * Handle ending session
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        // Deletes the token that was used for this request
        $request->user()->currentAccessToken()->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Logged out']);
    }
}
