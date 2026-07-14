<?php

namespace App\Http\Requests;

class UpdateProviderProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'about' => ['sometimes', 'string', 'max:1000'],
            'experience' => ['sometimes', 'integer', 'min:0', 'max:50'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}