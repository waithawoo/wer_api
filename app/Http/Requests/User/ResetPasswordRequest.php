<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ResetPasswordRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users',
            'password' => 'required|min:6|',
            'confirm_password' => 'required|same:password',
            'token' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('validation.custom.email.required'),
            'email.email' => __('validation.custom.email.email'),
            'email.exists' => __('validation.custom.email.exists'),

            'password.required' => __('validation.custom.password.required'),
            'password.min' => __('validation.custom.password.min'),

            'confirm_password.required' => __('validation.custom.confirm_password.required'),
            'confirm_password.same' => __('validation.custom.confirm_password.same'),

            'token.required' => __('validation.custom.token.required'),
            'token.string' => __('validation.custom.token.string'),
        ];
    }
}
