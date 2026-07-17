<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreProviderServiceAreaRequest;
use App\Http\Requests\UpdateProviderServiceAreaRequest;
use App\Models\ProviderServiceArea;

class ProviderServiceAreaController extends BaseApiController
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

        $serviceAreas = $providerProfile->serviceAreas()->latest()->get();

        return $this->successResponse($serviceAreas, 'Service areas fetched successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProviderServiceAreaRequest $request)
    {
        $providerProfile = auth()->user()->providerProfile;

        if (!$providerProfile) {
            return $this->errorResponse('Please complete your provider profile first.', null, 404);
        }

        $exists = $providerProfile->serviceAreas()->where('pincode', $request->pincode)->exists();

        if ($exists) {
            return $this->errorResponse('This service area already exists.', null, 409);
        }

        $serviceArea = $providerProfile->serviceAreas()->create([
            'pincode' => $request->pincode,
            'city' => $request->city,
            'state' => $request->state,
        ]);

        return $this->successResponse($serviceArea, 'Service area added successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProviderServiceArea $serviceArea)
    {
        $providerProfile = auth()->user()->providerProfile;

        if ($serviceArea->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        return $this->successResponse($serviceArea, 'Service area fetched successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProviderServiceAreaRequest $request, ProviderServiceArea $serviceArea)
    {
        $providerProfile = auth()->user()->providerProfile;

        if ($serviceArea->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        // Prevent duplicate pincodes
        if ($request->filled('pincode') && $request->pincode != $serviceArea->pincode) {
            $exists = $providerProfile->serviceAreas()->where('pincode', $request->pincode)->where('id', '!=', $serviceArea->id)->exists();

            if ($exists) {
                return $this->errorResponse('This service area already exists.', null, 409);
            }
        }

        $serviceArea->update([
            'pincode' => $request->pincode ?? $serviceArea->pincode,
            'city' => $request->city ?? $serviceArea->city,
            'state' => $request->state ?? $serviceArea->state,
        ]);

        return $this->successResponse($serviceArea, 'Service area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProviderServiceArea $serviceArea)
    {
        $providerProfile = auth()->user()->providerProfile;

        if ($serviceArea->provider_profile_id != $providerProfile->id) {
            return $this->errorResponse('Unauthorized.', null, 403);
        }

        $serviceArea->delete();

        return $this->successResponse(null, 'Service area deleted successfully.');
    }
}
