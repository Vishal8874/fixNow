<?php

namespace App\Http\Requests;

class StoreReviewRequest extends BaseApiRequest
{
     
    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,5'],

            'comment' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
