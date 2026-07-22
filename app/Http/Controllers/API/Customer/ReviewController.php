<?php

namespace App\Http\Controllers\API\Customer;

use App\Models\Booking;
use App\Services\ReviewService;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Http\Requests\UpdateReviewRequest;

class ReviewController extends BaseApiController
{
    public function __construct(protected ReviewService $reviewService) {}

    public function store(StoreReviewRequest $request, Booking $booking)
    {
        $review = $this->reviewService->store(auth()->user(), $booking, $request->validated());

        return $this->successResponse($review, 'Review submitted successfully.', 201);
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $review = $this->reviewService->update(auth()->user(), $review, $request->validated());

        return $this->successResponse($review, 'Review updated successfully.');
    }
}
