<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateProviderServiceRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [

            'service_category_id' => [

                'sometimes',

                Rule::exists('service_categories', 'id')
                    ->where('status', true),

            ],

            'base_price' => [
                'sometimes',
                'numeric',
                'min:0'
            ],

            'is_available' => [
                'sometimes',
                'boolean'
            ],

        ];
    }
}