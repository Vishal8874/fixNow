<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;

class StoreProviderServiceRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'service_category_id' => ['required', Rule::exists('service_categories', 'id')->where('status', true)],

            'base_price' => ['required', 'numeric', 'min:0'],

            'is_available' => ['nullable', 'boolean'],
        ];
    }
}
