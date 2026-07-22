<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cancel_reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    /**
     * Custom Validation Messages
     */
    public function messages(): array
    {
        return [
            'cancel_reason.required' => 'Please provide a cancellation reason.',
            'cancel_reason.string' => 'The cancellation reason must be a valid string.',
            'cancel_reason.min' => 'The cancellation reason must be at least 5 characters.',
            'cancel_reason.max' => 'The cancellation reason may not be greater than 500 characters.',
        ];
    }
}
