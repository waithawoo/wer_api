<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\Base64Image;
use Illuminate\Support\Facades\Lang;

class UpdateRequest extends FormRequest
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
            'name' => 'nullable|string',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'photo' => [
                'nullable',
                'string',
                new Base64Image(),
            ],
            'email' => 'nullable|email|unique:users',
        ];
    }

    public function messages()
    {
        return [
            'name.string' => __('validation.custom.name.string'),

            'phone.regex' => __('validation.custom.phone.regex'),
            'phone.min' => __('validation.custom.phone.min'),

            'photo.string' => __('validation.custom.photo.string'),
            'photo.base64_image' => __('validation.custom.photo.base64_image'),

            'email.email' => __('validation.custom.email.email'),
            'email.unique' => __('validation.custom.email.unique'),
        ];
    }
}
