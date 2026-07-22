<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    public function store(User $customer, Booking $booking, array $data): Review
    {
        return DB::transaction(function () use ($customer, $booking, $data) {
            /*
            |--------------------------------------------------------------------------
            | Ensure Customer Owns Booking
            |--------------------------------------------------------------------------
            */

            if ($booking->customer_id !== $customer->id) {
                throw ValidationException::withMessages([
                    'booking' => ['Booking not found.'],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Booking Must Be Completed
            |--------------------------------------------------------------------------
            */

            if ($booking->status !== BookingStatus::COMPLETED) {
                throw ValidationException::withMessages([
                    'booking' => ['You can review only completed bookings.'],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Review
            |--------------------------------------------------------------------------
            */

            if ($booking->review()->exists()) {
                throw ValidationException::withMessages([
                    'review' => ['You have already reviewed this booking.'],
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Create Review
            |--------------------------------------------------------------------------
            */

            $review = Review::create([
                'booking_id' => $booking->id,

                'customer_id' => $customer->id,

                'provider_profile_id' => $booking->provider_profile_id,

                'rating' => $data['rating'],

                'comment' => $data['comment'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Provider Average Rating
            |--------------------------------------------------------------------------
            */

            $this->updateAverageRating($booking->providerProfile);

            return $review->load(['customer', 'booking', 'providerProfile']);
        });
    }
    public function update(User $customer, Review $review, array $data): Review
    {
        /*
    |--------------------------------------------------------------------------
    | Ensure Customer Owns Review
    |--------------------------------------------------------------------------
    */

        if ($review->customer_id !== $customer->id) {
            throw ValidationException::withMessages([
                'review' => ['Review not found.'],
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Update Review
    |--------------------------------------------------------------------------
    */

        $review->update([
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        $this->updateAverageRating($review->providerProfile);

        return $review->fresh(['customer', 'booking', 'providerProfile']);
    }

    public function getProviderReviews(User $provider)
    {
        if (!$provider->providerProfile) {
            throw ValidationException::withMessages([
                'provider' => ['Provider profile not found.'],
            ]);
        }

        return Review::with(['customer:id,name'])
            ->where('provider_profile_id', $provider->providerProfile->id)
            ->latest()
            ->paginate(10);
    }

    /**
     * Update Provider Average Rating
     */
    private function updateAverageRating($providerProfile): void
    {
        $providerProfile->update([
            'average_rating' => round($providerProfile->reviews()->avg('rating') ?? 0, 1),
        ]);
    }
}
