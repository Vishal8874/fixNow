<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateServiceCategoryRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('service_categories', 'name')->ignore($this->route('category')),
            ],

            'description' => ['nullable', 'string'],

            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],

            'status' => ['nullable', 'boolean'],
        ];
    }
}