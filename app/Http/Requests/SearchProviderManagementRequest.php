<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Enums\UserStatus;

class SearchProviderManagementRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [

            'status' => [
                'nullable',
                Rule::enum(UserStatus::class),
            ],

            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:5',
                'max:100',
            ],
        ];
    }
}