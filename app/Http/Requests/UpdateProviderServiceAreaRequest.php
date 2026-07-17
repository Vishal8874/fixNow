<?php

namespace App\Http\Requests;

class UpdateProviderServiceAreaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pincode' => [
                'sometimes',
                'digits:6',
            ],

            'city' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'state' => [
                'sometimes',
                'string',
                'max:100',
            ],
        ];
    }
}