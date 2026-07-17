<?php

namespace App\Http\Controllers\API\Customer;

use App\Enums\UserStatus;
use App\Filters\ProviderFilter;
use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\SearchProviderRequest;
use App\Models\ProviderProfile;

class CustomerProviderController extends BaseApiController
{
    public function index(SearchProviderRequest $request)
    {
        $providers = ProviderProfile::query()
            ->whereHas('user', function ($query) {
                $query->where('status', UserStatus::ACTIVE);
            })
            ->whereHas('services', function ($query) {
                $query->where('is_available', true);
            })
            ->with([
                'user',
                'services' => function ($query) {
                    $query->where('is_available', true)
                        ->with('category');
                },
                'serviceAreas',
            ]);

        $providers = (new ProviderFilter($request->validated()))
            ->apply($providers);

        $providers = $providers->paginate(10);

        return $this->successResponse(
            $providers,
            'Providers fetched successfully.'
        );
    }
}