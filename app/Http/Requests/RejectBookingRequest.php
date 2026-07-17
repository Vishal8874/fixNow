<?php

namespace App\Http\Requests;

class RejectBookingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'reject_reason' => [
                'required',
                'string',
                'min:5',
                'max:500',
            ],
        ];
    }
}