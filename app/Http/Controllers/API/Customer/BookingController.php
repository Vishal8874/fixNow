<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use App\Models\Booking;

class BookingController extends BaseApiController
{
    public function __construct(protected BookingService $bookingService) {}

    public function index()
    {
        $bookings = $this->bookingService->getCustomerBookings(auth()->id());

        return $this->successResponse($bookings, 'Bookings fetched successfully.');
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = $this->bookingService->create(auth()->user(), $request->validated());

        return $this->successResponse($booking, 'Booking created successfully.', 201);
    }

    public function show(Booking $booking)
    {
        $booking = $this->bookingService->getBookingDetails(auth()->id(), $booking);

        return $this->successResponse($booking, 'Booking details fetched successfully.');
    }

    // public function destroy(Booking $booking)
    // {
    //     $booking = $this->bookingService->cancelBooking(auth()->id(), $booking);

    //     return $this->successResponse($booking, 'Booking cancelled successfully.');
    // }
}
