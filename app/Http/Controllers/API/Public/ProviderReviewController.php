<?php

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\API\BaseApiController;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Services\ReviewService;

class ProviderReviewController extends BaseApiController
{
    public function __construct(protected ReviewService $reviewService) {}

    public function index(User $provider)
    {
        $reviews = $this->reviewService->getProviderReviews($provider);

        return $this->successResponse($reviews, 'Provider reviews fetched successfully.');
    }
}
