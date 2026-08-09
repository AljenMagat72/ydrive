<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class DriverLoginRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $rules = [
      'phoneNumber' => 'required|phone',
    ];

    if (config('recaptchav3.enabled')) {
      $rules['captcha'] = 'required|recaptchav3:register,0.3';
    }

    return $rules;
  }
}
