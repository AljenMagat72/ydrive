<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'form.name' => ['required', 'string', 'max:255'],

            'form.email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('admins', 'email'),
            ],

            'form.password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'form.token' => ['required', 'string'],
        ];

        if (config('recaptchav3.enabled')) {
            $rules['recaptcha'] = [
                Rule::requiredIf(app()->environment('production')),
                'recaptchav3:register,0.3',
            ];
        }

        return $rules;
    }
}
