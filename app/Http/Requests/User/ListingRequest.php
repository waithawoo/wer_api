<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Lang;

class ListingRequest extends FormRequest
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
            'page' => 'integer',
            'per_page' => 'integer',
            'q' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'page.integer' => __('validation.custom.page.integer'),
            'per_page.integer' => __('validation.custom.per_page.integer'),
            'q.string' => __('validation.custom.q.string'),
        ];
    }
}
