<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required' => __('validation.custom.current_password.required'),

            'new_password.required' => __('validation.custom.new_password.required'),
            'new_password.min' => __('validation.custom.new_password.min'),
            'new_password.confirmed' => __('validation.custom.new_password.confirmed'),

            'new_password_confirmation.required' => __('validation.custom.new_password_confirmation.required'),
            'new_password_confirmation.min' => __('validation.custom.new_password_confirmation.min'),
        ];
    }
}
