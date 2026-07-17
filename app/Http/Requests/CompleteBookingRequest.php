<?php

namespace App\Http\Requests;

class CompleteBookingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'final_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ];
    }
}