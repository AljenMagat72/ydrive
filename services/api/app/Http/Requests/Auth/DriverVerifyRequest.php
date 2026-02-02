<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DriverVerifyRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {

    $rules = [
      'code' => 'required|string|size:6',
    ];

    if (config('recaptchav3.enabled')) {
      $rules['captcha'] = 'required|recaptchav3:verify,0.5';
    }

    return $rules;
  }
}
