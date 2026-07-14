<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Provider\ProviderController;
use App\Http\Controllers\API\Provider\ProviderServiceController;
use App\Http\Controllers\API\Admin\ServiceCategoryController;

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

    //Providers Route
    Route::middleware(['auth:sanctum', 'role:provider'])
        ->prefix('provider')
        ->group(function () {
            Route::post('/profile', [ProviderController::class, 'storeProfile']);
            Route::put('/profile', [ProviderController::class, 'updateProfile']);
            Route::get('/profile', [ProviderController::class, 'showProfile']);
            Route::apiResource('services', ProviderServiceController::class);
        });

    //Admin Routes
    Route::middleware(['auth:sanctum', 'role:admin'])
        ->prefix('admin')
        ->group(function () {
            Route::apiResource('categories', ServiceCategoryController::class);
        });
});
