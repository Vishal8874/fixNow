<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Requests\BlockProviderRequest;
use App\Http\Requests\RejectProviderRequest;
use App\Http\Requests\SearchProviderManagementRequest;
use App\Models\User;
use App\Services\AdminProviderService;

class ProviderManagementController extends BaseApiController
{
    public function __construct(protected AdminProviderService $providerService) {}

    public function index(SearchProviderManagementRequest $request)
    {
        $providers = $this->providerService->getProviders($request->validated());

        return $this->successResponse($providers, 'Providers fetched successfully.');
    }

    public function show(User $provider)
    {
        $provider = $this->providerService->getProviderDetails($provider);

        return $this->successResponse($provider, 'Provider details fetched successfully.');
    }

    public function approve(User $provider)
    {
        $provider = $this->providerService->approveProvider($provider);

        return $this->successResponse($provider, 'Provider approved successfully.');
    }
    public function reject(RejectProviderRequest $request, User $provider)
    {
        $provider = $this->providerService->rejectProvider($provider, $request->validated());

        return $this->successResponse($provider, 'Provider rejected successfully.');
    }

    public function block(BlockProviderRequest $request, User $provider)
    {
        $provider = $this->providerService->blockProvider($provider, $request->validated());

        return $this->successResponse($provider, 'Provider blocked successfully.');
    }
    public function unblock(User $provider)
    {
        $provider = $this->providerService->unblockProvider($provider);

        return $this->successResponse($provider, 'Provider unblocked successfully.');
    }
}
