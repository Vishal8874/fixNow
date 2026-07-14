<?php

namespace App\Http\Requests;

class StoreServiceCategoryRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name'],

            'description' => ['nullable', 'string'],

            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg', 'max:2048'],

            'status' => ['nullable', 'boolean'],
        ];
    }
}