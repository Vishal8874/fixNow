<?php

namespace App\Http\Requests;

class StoreProviderProfileRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'about' => ['required', 'string', 'max:1000'],
            'experience' => ['required', 'integer', 'min:0', 'max:50'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}