<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreBookingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [

            'provider_service_id' => [
                'required',
                Rule::exists('provider_services', 'id'),
            ],

            'service_area_id' => [
                'required',
                Rule::exists('provider_service_areas', 'id'),
            ],

            'scheduled_at' => [
                'required',
                'date',
                'after:now',
            ],

            'customer_name' => [
                'required',
                'string',
                'max:255',
            ],

            'customer_email' => [
                'required',
                'email',
                'max:255',
            ],

            'customer_phone' => [
                'required',
                'digits:10',
            ],

            'customer_address' => [
                'required',
                'string',
            ],

            'customer_city' => [
                'required',
                'string',
                'max:100',
            ],

            'customer_state' => [
                'required',
                'string',
                'max:100',
            ],

            'customer_pincode' => [
                'required',
                'digits:6',
            ],

            'issue_description' => [
                'required',
                'string',
                'min:10',
            ],
        ];
    }
}