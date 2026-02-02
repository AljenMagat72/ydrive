<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\RegistrationToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Create user
     * @param Request $request
     * @return JsonResponse
     */
    public function store(RegistrationRequest $request)
    {
        $data = $request->validated();

        $token = data_get($data, 'form.token');

        $tokenExist = TokenRegistration::$where('token', $token)->first();

        if (!$tokenExist) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed, please contact your developer.'
            ], 401);
        }

        $user = User::create($data['form']);

        $user->email_verified_at = now();
        $user->save();

        RegistrationToken::where('token', $token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin registration successful. Redirecting you to the login page…',
        ], 201);
    }
}
