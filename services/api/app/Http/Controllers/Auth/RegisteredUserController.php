<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\RegistrationToken;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        $tokenExists = RegistrationToken::where('token', $token)->first();

        if (!$tokenExists) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed, please contact your developer.'
            ], 401);
        }

        $user = Admin::create($data['form']);

        $user->email_verified_at = now();
        $user->save();

        RegistrationToken::where('token', $token)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin registration successful. Redirecting you to the login page…',
        ], 201);
    }
}
