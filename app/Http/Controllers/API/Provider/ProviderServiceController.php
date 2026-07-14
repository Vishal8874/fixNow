<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreProviderServiceRequest;
use App\Http\Requests\UpdateProviderServiceRequest;
use App\Models\ProviderProfile;
use App\Models\ProviderService;

class ProviderServiceController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $providerProfile = auth()->user()->providerProfile;

        if (!$providerProfile) {
            return $this->errorResponse('Please complete your provider profile first.', null, 404);
        }

        $services = $providerProfile->services()->with('category')->latest()->get();

        return $this->successResponse($services, 'Services fetched successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProviderServiceRequest $request)
    {
        $providerProfile = auth()->user()->providerProfile;

        if (!$providerProfile) {
            return $this->errorResponse('Please complete your provider profile first.', null, 404);
        }

        $exists = $providerProfile->services()->where('service_category_id', $request->service_category_id)->exists();

        if ($exists) {
            return $this->errorResponse('This service is already added.', null, 409);
        }

        $service = $providerProfile->services()->create([
            'service_category_id' => $request->service_category_id,
            'base_price' => $request->base_price,
            'is_available' => $request->boolean('is_available', true),
        ]);

        return $this->successResponse($service, 'Service added successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProviderService $service)
    {
        $providerProfile = auth()->user()->providerProfile;

        if ($service->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        $service->load('category');

        return $this->successResponse($service, 'Service fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProviderServiceRequest $request, ProviderService $service)
    {
        $providerProfile = auth()->user()->providerProfile;

        if (!$providerProfile) {
            return $this->errorResponse('Please complete your provider profile first.', null, 404);
        }

        if ($service->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        // Check duplicate category only if category is being changed
        if ($request->filled('service_category_id') && $request->service_category_id != $service->service_category_id) {
            $exists = $providerProfile->services()->where('service_category_id', $request->service_category_id)->where('id', '!=', $service->id)->exists();

            if ($exists) {
                return $this->errorResponse('This service is already added.', null, 409);
            }
        }

        $service->update([
            'service_category_id' => $request->service_category_id ?? $service->service_category_id,
            'base_price' => $request->base_price ?? $service->base_price,
            'is_available' => $request->has('is_available') ? $request->boolean('is_available') : $service->is_available,
        ]);

        return $this->successResponse($service->fresh()->load('category'), 'Service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProviderService $service)
    {
        $providerProfile = auth()->user()->providerProfile;

        if ($service->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        $service->delete();

        return $this->successResponse(null, 'Service deleted successfully.');
    }
}
