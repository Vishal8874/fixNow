<?php

namespace App\Http\Requests;

class StoreProviderServiceAreaRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'pincode' => [
                'required',
                'digits:6',
            ],

            'city' => [
                'required',
                'string',
                'max:100',
            ],

            'state' => [
                'required',
                'string',
                'max:100',
            ],
        ];
    }
}