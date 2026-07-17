<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\CompleteBookingRequest;
use App\Http\Requests\RejectBookingRequest;
use App\Models\Booking;
use App\Services\BookingService;

class ProviderBookingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService) {}

    /**
     * Provider Booking List
     */
    public function index()
    {
        $bookings = $this->bookingService->getProviderBookings(auth()->user());

        return $this->successResponse($bookings, 'Bookings fetched successfully.');
    }

    /**
     * Provider Booking Details
     */
    public function show(Booking $booking)
    {
        $booking = $this->bookingService->getProviderBookingDetails(auth()->user(), $booking);

        return $this->successResponse($booking, 'Booking details fetched successfully.');
    }

    public function accept(Booking $booking)
    {
        $booking = $this->bookingService->acceptBooking(auth()->user(), $booking);

        return $this->successResponse($booking, 'Booking accepted successfully.');
    }

    public function reject(RejectBookingRequest $request, Booking $booking)
    {
        $booking = $this->bookingService->rejectBooking(auth()->user(), $booking, $request->validated());

        return $this->successResponse($booking, 'Booking rejected successfully.');
    }

    public function complete(CompleteBookingRequest $request, Booking $booking)
    {
        $booking = $this->bookingService->completeBooking(auth()->user(), $booking, $request->validated());

        return $this->successResponse($booking, 'Booking completed successfully.');
    }
}
