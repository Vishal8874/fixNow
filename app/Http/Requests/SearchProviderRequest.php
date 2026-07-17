<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class SearchProviderRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'category_id' => [
                'nullable',
                Rule::exists('service_categories', 'id')
                    ->where('status', true),
            ],

            'city' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pincode' => [
                'nullable',
                'digits:6',
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'rating_desc',
                    'experience_desc',
                    'name_asc',
                    'name_desc',
                    'price_low_high',
                    'price_high_low',
                ]),
            ],
        ];
    }
}