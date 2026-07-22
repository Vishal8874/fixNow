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
    /**
     * Create Booking
     */
    public function create(User $customer, array $data): Booking
    {
        return DB::transaction(function () use ($customer, $data) {

            /*
            |--------------------------------------------------------------------------
            | Fetch Provider Service
            |--------------------------------------------------------------------------
            */

            $providerService = ProviderService::with([
                'providerProfile.user',
            ])->findOrFail($data['provider_service_id']);

            /*
            |--------------------------------------------------------------------------
            | Validate Service Area
            |--------------------------------------------------------------------------
            */

            $serviceArea = $providerService->providerProfile
                ->serviceAreas()
                ->find($data['service_area_id']);

            if (!$serviceArea) {
                throw ValidationException::withMessages([
                    'service_area_id' => [
                        'The selected service area is invalid.',
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Provider Must Be Active
            |--------------------------------------------------------------------------
            */

            if ($providerService->providerProfile->user->status !== \App\Enums\UserStatus::ACTIVE) {
                throw ValidationException::withMessages([
                    'provider' => [
                        'This provider is currently unavailable.',
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Existing Active Booking
            |--------------------------------------------------------------------------
            */

            $existingBooking = Booking::where('customer_id', $customer->id)
                ->where('provider_service_id', $providerService->id)
                ->whereIn('status', [
                    BookingStatus::PENDING,
                    BookingStatus::ACCEPTED,
                ])
                ->exists();

            if ($existingBooking) {
                throw ValidationException::withMessages([
                    'booking' => [
                        'You already have an active booking for this service. Please wait until it is completed or cancelled before booking it again.',
                    ],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Booking
            |--------------------------------------------------------------------------
            */

            $booking = Booking::create([

                'booking_number' => $this->generateBookingNumber(),

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

            return $this->refreshBooking($booking);
        });
    }

    /**
     * Generate Booking Number
     */
    private function generateBookingNumber(): string
    {
        return 'FN-' . now()->format('YmdHis') . rand(1000, 9999);
    }

    /**
     * Customer Booking List
     */
    public function getCustomerBookings(int $customerId)
    {
        return Booking::with([
            'providerProfile.user',
            'providerService.category',
            'serviceArea',
        ])
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate(10);
    }

    /**
     * Customer Booking Details
     */
    public function getBookingDetails(int $customerId, Booking $booking): Booking
    {
        $this->ensureCustomerOwnsBookingById($customerId, $booking);

        return $this->refreshBooking($booking);
    }

    /**
     * Cancel Booking
     */
    public function cancelBooking(
        User $customer,
        Booking $booking,
        array $data
    ): Booking {

        $this->ensureCustomerOwnsBooking($customer, $booking);

        if (!in_array($booking->status, [
            BookingStatus::PENDING,
            BookingStatus::ACCEPTED,
        ])) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Only pending or accepted bookings can be cancelled.',
                ],
            ]);
        }

        $booking->update([
            'status' => BookingStatus::CANCELLED,
            'cancel_reason' => $data['cancel_reason'],
        ]);

        return $this->refreshBooking($booking);
    }

    /**
     * Provider Booking List
     */
    public function getProviderBookings(User $provider)
    {
        return Booking::with([
            'customer',
            'providerService.category',
            'serviceArea',
        ])
            ->where(
                'provider_profile_id',
                $provider->providerProfile->id
            )
            ->latest()
            ->paginate(10);
    }

    /**
     * Provider Booking Details
     */
    public function getProviderBookingDetails(
        User $provider,
        Booking $booking
    ): Booking {

        $this->ensureProviderOwnsBooking($provider, $booking);

        return $this->refreshBooking($booking);
    }

    /**
     * Accept Booking
     */
    public function acceptBooking(
        User $provider,
        Booking $booking
    ): Booking {

        $this->ensureProviderOwnsBooking($provider, $booking);

        if ($booking->status !== BookingStatus::PENDING) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Only pending bookings can be accepted.',
                ],
            ]);
        }

        $booking->update([
            'status' => BookingStatus::ACCEPTED,
        ]);

        return $this->refreshBooking($booking);
    }

    /**
     * Reject Booking
     */
    public function rejectBooking(
        User $provider,
        Booking $booking,
        array $data
    ): Booking {

        $this->ensureProviderOwnsBooking($provider, $booking);

        if ($booking->status !== BookingStatus::PENDING) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Only pending bookings can be rejected.',
                ],
            ]);
        }

        $booking->update([
            'status' => BookingStatus::REJECTED,
            'reject_reason' => $data['reject_reason'],
        ]);

        return $this->refreshBooking($booking);
    }

    /**
     * Complete Booking
     */
    public function completeBooking(
        User $provider,
        Booking $booking,
        array $data
    ): Booking {

        $this->ensureProviderOwnsBooking($provider, $booking);

        if ($booking->status !== BookingStatus::ACCEPTED) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Only accepted bookings can be completed.',
                ],
            ]);
        }

        $booking->update([
            'status' => BookingStatus::COMPLETED,
            'final_price' => $data['final_price'],
            'completed_at' => now(),
        ]);

        return $this->refreshBooking($booking);
    }

    /**
     * Ensure Customer Owns Booking
     */
    private function ensureCustomerOwnsBooking(
        User $customer,
        Booking $booking
    ): void {

        if ($booking->customer_id !== $customer->id) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Booking not found.',
                ],
            ]);
        }
    }

    /**
     * Ensure Customer Owns Booking (By Id)
     */
    private function ensureCustomerOwnsBookingById(
        int $customerId,
        Booking $booking
    ): void {

        if ($booking->customer_id !== $customerId) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Booking not found.',
                ],
            ]);
        }
    }

    /**
     * Ensure Provider Owns Booking
     */
    private function ensureProviderOwnsBooking(
        User $provider,
        Booking $booking
    ): void {

        if (
            !$provider->providerProfile ||
            $booking->provider_profile_id != $provider->providerProfile->id
        ) {
            throw ValidationException::withMessages([
                'booking' => [
                    'Booking not found.',
                ],
            ]);
        }
    }

    /**
     * Refresh Booking
     */
    private function refreshBooking(
        Booking $booking
    ): Booking {

        return $booking->fresh([
            'customer',
            'providerProfile.user',
            'providerService.category',
            'serviceArea',
        ]);
    }
}