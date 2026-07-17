<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\ProviderService;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(User $customer, array $data): Booking
    {
        return DB::transaction(function () use ($customer, $data) {
            //Fetch Provider Service

            $providerService = ProviderService::with('providerProfile')->findOrFail($data['provider_service_id']);

            //Validate Service Area

            $serviceArea = $providerService->providerProfile->serviceAreas()->find($data['service_area_id']);

            if (!$serviceArea) {
                throw ValidationException::withMessages([
                    'service_area_id' => ['The selected service area is invalid.'],
                ]);
            }

            //Check Active Booking

            $existingBooking = Booking::where('customer_id', $customer->id)
                ->where('provider_service_id', $providerService->id)
                ->whereIn('status', [BookingStatus::PENDING, BookingStatus::ACCEPTED])
                ->first();

            if ($existingBooking) {
                throw ValidationException::withMessages([
                    'booking' => ['You already have an active booking for this service. Please wait until it is completed or cancelled before booking it again.'],
                ]);
            }

            //Generate Booking Number

            $bookingNumber = $this->generateBookingNumber();

            //Create Booking

            return Booking::create([
                'booking_number' => $bookingNumber,

                'customer_id' => $customer->id,

                'provider_profile_id' => $providerService->provider_profile_id,

                'provider_service_id' => $providerService->id,

                'service_area_id' => $serviceArea->id,

                'scheduled_at' => $data['scheduled_at'],

                'customer_name' => $data['customer_name'],

                'customer_email' => $data['customer_email'],

                'customer_phone' => $data['customer_phone'],

                'customer_address' => $data['customer_address'],

                'customer_city' => $data['customer_city'],

                'customer_state' => $data['customer_state'],

                'customer_pincode' => $data['customer_pincode'],

                'issue_description' => $data['issue_description'],

                'estimated_price' => $providerService->base_price,

                'status' => BookingStatus::PENDING,
            ]);
        });
    }

    private function generateBookingNumber(): string
    {
        return 'FN-' . now()->format('YmdHis') . rand(1000, 9999);
    }

    //Get Customer Bookings
    public function getCustomerBookings(int $customerId)
    {
        return Booking::with(['providerProfile.user', 'providerService.category', 'serviceArea'])
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate(10);
    }

    //Get Booking Details
    public function getBookingDetails(int $customerId, Booking $booking): Booking
    {
        if ($booking->customer_id !== $customerId) {
            throw ValidationException::withMessages([
                'booking' => ['Booking not found.'],
            ]);
        }

        return $booking->load(['providerProfile.user', 'providerService.category', 'serviceArea']);
    }

    //cancel Booking
    // public function cancelBooking(int $customerId, Booking $booking): Booking
    // {
    //     if ($booking->customer_id !== $customerId) {
    //         throw ValidationException::withMessages([
    //             'booking' => ['Booking not found.'],
    //         ]);
    //     }

    //     if (!in_array($booking->status, [BookingStatus::PENDING, BookingStatus::ACCEPTED])) {
    //         throw ValidationException::withMessages([
    //             'booking' => ['This booking cannot be cancelled.'],
    //         ]);
    //     }

    //     $booking->update([
    //         'status' => BookingStatus::CANCELLED,
    //         'cancel_reason' => 'Cancelled by customer',
    //     ]);

    //     return $booking;
    // }


    
}
