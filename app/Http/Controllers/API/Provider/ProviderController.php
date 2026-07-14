<?php

namespace App\Http\Controllers\API\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\StoreProviderProfileRequest;
use App\Models\ProviderProfile;
use App\Enums\UserRole;
use App\Http\Requests\UpdateProviderProfileRequest;
use Illuminate\Support\Facades\Storage;

class ProviderController extends BaseApiController
{
    //store profile
    public function storeProfile(StoreProviderProfileRequest $request)
    {
        $user = auth()->user();

        if ($user->role !== UserRole::PROVIDER) {
            return $this->errorResponse('Only providers can create a provider profile.', null, 403);
        }

        if ($user->providerProfile) {
            return $this->errorResponse('Provider profile already exists.', null, 409);
        }

        $profileImage = null;

        if ($request->hasFile('profile_image')) {
            $profileImage = $request->file('profile_image')->store('provider_profiles', 'public');
        }

        $profile = ProviderProfile::create([
            'user_id' => $user->id,
            'about' => $request->about,
            'experience' => $request->experience,
            'profile_image' => $profileImage,
        ]);

        return $this->successResponse($profile, 'Provider profile created successfully.', 201);
    }

    public function updateProfile(UpdateProviderProfileRequest $request)
    {
        // dd($request->all(), $request->file());

        $user = auth()->user();

        $profile = $user->providerProfile;

        if (!$profile) {
            return $this->errorResponse('Provider profile not found.', null, 404);
        }

        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $profile->profile_image = $request->file('profile_image')->store('provider_profiles', 'public');
        }

        if ($request->filled('about')) {
            $profile->about = $request->about;
        }

        if ($request->filled('experience')) {
            $profile->experience = $request->experience;
        }

        $profile->save();

        return $this->successResponse($profile, 'Provider profile updated successfully.');
    }

    public function showProfile()
    {
        $user = auth()->user();

        if ($user->role !== UserRole::PROVIDER) {
            return $this->errorResponse('Only providers can access this profile.', null, 403);
        }

        $profile = $user->providerProfile;

        if (!$profile) {
            return $this->errorResponse('Provider profile not found.', null, 404);
        }

        return $this->successResponse($profile, 'Provider profile fetched successfully.');
    }
}
