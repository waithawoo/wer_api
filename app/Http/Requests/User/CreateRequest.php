<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Base64Image;
use Illuminate\Support\Facades\Lang;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'photo' => [
                'required',
                'string',
                new Base64Image(),
            ],
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('validation.custom.name.required'),
            'name.string' => __('validation.custom.name.string'),
            
            'phone.required' => __('validation.custom.phone.required'),
            'phone.regex' => __('validation.custom.phone.regex'),
            'phone.min' => __('validation.custom.phone.min'),

            'photo.required' => __('validation.custom.photo.required'),
            'photo.string' => __('validation.custom.photo.string'),
            'photo.base64_image' => __('validation.custom.photo.base64_image'),

            'email.required' => __('validation.custom.email.required'),
            'email.email' => __('validation.custom.email.email'),
            'email.unique' => __('validation.custom.email.unique'),

            'password.required' => __('validation.custom.password.required'),
            'password.min' => __('validation.custom.password.min'),
            'password.confirmed' => __('validation.custom.password.confirmed'),

            'password_confirmation.required' => __('validation.custom.password_confirmation.required'),
            'password_confirmation.min' => __('validation.custom.password_confirmation.min'),
        ];
    }
}
