<?php

namespace App\Http\Requests;

class BlockProviderRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
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

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'admin_remark.required' => 'Please provide a reason for blocking the provider.',
            'admin_remark.min' => 'The blocking reason must be at least 5 characters.',
            'admin_remark.max' => 'The blocking reason may not be greater than 500 characters.',
        ];
    }
}