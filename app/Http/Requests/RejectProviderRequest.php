<?php

namespace App\Http\Requests;

class RejectProviderRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'admin_remark' => [
                'required',
                'string',
                'min:5',
                'max:500',
            ],
        ];
    }
}