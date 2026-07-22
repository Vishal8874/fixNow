<?php

use App\Http\Controllers\API\Admin\ProviderManagementController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Provider\ProviderController;
use App\Http\Controllers\API\Provider\ProviderServiceController;
use App\Http\Controllers\API\Provider\ProviderServiceAreaController;
use App\Http\Controllers\API\Admin\ServiceCategoryController;
use App\Http\Controllers\API\Customer\BookingController;
use App\Http\Controllers\API\Customer\CategoryController;
use App\Http\Controllers\API\Customer\CustomerProviderController;
use App\Http\Controllers\API\Customer\ReviewController;
use App\Http\Controllers\API\Provider\ProviderBookingController;
use App\Http\Controllers\API\Public\ProviderReviewController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/provider/register', [AuthController::class, 'providerRegister']);

        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/google/redirect', [AuthController::class, 'googleRedirect']);
        Route::get('/google/callback', [AuthController::class, 'googleCallback']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);

            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // Public Customer Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/providers', [CustomerProviderController::class, 'index']);
    Route::get('providers/{provider}/reviews', [ProviderReviewController::class, 'index']);

    //Providers Route
    Route::middleware(['auth:sanctum', 'role:provider'])
        ->prefix('provider')
        ->group(function () {
            Route::post('/profile', [ProviderController::class, 'storeProfile']);
            Route::put('/profile', [ProviderController::class, 'updateProfile']);
            Route::get('/profile', [ProviderController::class, 'showProfile']);
            Route::apiResource('services', ProviderServiceController::class);
            Route::apiResource('service-areas', ProviderServiceAreaController::class)->parameters([
                'service-areas' => 'serviceArea',
            ]);
            Route::prefix('bookings')->group(function () {
                Route::get('/', [ProviderBookingController::class, 'index']);
                Route::get('/{booking}', [ProviderBookingController::class, 'show']);
                Route::patch('/{booking}/accept', [ProviderBookingController::class, 'accept']);
                Route::patch('/{booking}/reject', [ProviderBookingController::class, 'reject']);
                Route::patch('/{booking}/complete', [ProviderBookingController::class, 'complete']);
            });
        });

    //Admin Routes
    Route::middleware(['auth:sanctum', 'role:admin'])
        ->prefix('admin')
        ->group(function () {
            Route::apiResource('categories', ServiceCategoryController::class);
            Route::get('/providers', [ProviderManagementController::class, 'index']);
            Route::get('/providers/{provider}', [ProviderManagementController::class, 'show']);
            Route::patch('/providers/{provider}/approve', [ProviderManagementController::class, 'approve']);
            Route::patch('/providers/{provider}/reject', [ProviderManagementController::class, 'reject']);
            Route::patch('/providers/{provider}/block', [ProviderManagementController::class, 'block']);
            Route::patch('/providers/{provider}/unblock', [ProviderManagementController::class, 'unblock']);
        });

    //Customer Routes
    Route::middleware(['auth:sanctum', 'role:customer'])
        ->prefix('customer')
        ->group(function () {
            Route::apiResource('bookings', BookingController::class)->only(['index', 'store', 'show']);

            Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel']);

            Route::post('bookings/{booking}/review', [ReviewController::class, 'store']);
            Route::put('reviews/{review}', [ReviewController::class, 'update']);
        });
});
