<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Filters\AdminProviderFilter;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdminProviderService
{
    /**
     * Get Providers List
     */
    public function getProviders(array $filters)
    {
        $query = User::query()
            ->where('role', UserRole::PROVIDER)
            ->with([
                'providerProfile',
                'providerProfile.services.category',
                'providerProfile.serviceAreas',
            ]);

        $query = (new AdminProviderFilter($filters))->apply($query);

        return $query->latest()->paginate(
            $filters['per_page'] ?? 10
        );
    }

    /**
     * Get Provider Details
     */
    public function getProviderDetails(User $provider): User
    {
        $this->ensureProvider($provider);

        return $this->refreshProvider($provider);
    }

    /**
     * Approve Provider
     * Pending -> Active
     */
    public function approveProvider(User $provider): User
    {
        $this->ensureProvider($provider);

        if ($provider->status !== UserStatus::PENDING) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Only pending providers can be approved.',
                ],
            ]);
        }

        $provider->update([
            'status' => UserStatus::ACTIVE,
            'admin_remark' => null,
        ]);

        return $this->refreshProvider($provider);
    }

    /**
     * Reject Provider
     * Pending -> Rejected
     */
    public function rejectProvider(User $provider, array $data): User
    {
        $this->ensureProvider($provider);

        if ($provider->status !== UserStatus::PENDING) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Only pending providers can be rejected.',
                ],
            ]);
        }

        $provider->update([
            'status' => UserStatus::REJECTED,
            'admin_remark' => $data['admin_remark'],
        ]);

        return $this->refreshProvider($provider);
    }

    /**
     * Block Provider
     * Active -> Blocked
     */
    public function blockProvider(User $provider, array $data): User
    {
        $this->ensureProvider($provider);

        if ($provider->status !== UserStatus::ACTIVE) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Only active providers can be blocked.',
                ],
            ]);
        }

        $provider->update([
            'status' => UserStatus::BLOCKED,
            'admin_remark' => $data['admin_remark'],
        ]);

        return $this->refreshProvider($provider);
    }

    /**
     * Unblock Provider
     * Blocked -> Active
     */
    public function unblockProvider(User $provider): User
    {
        $this->ensureProvider($provider);

        if ($provider->status !== UserStatus::BLOCKED) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Only blocked providers can be unblocked.',
                ],
            ]);
        }

        $provider->update([
            'status' => UserStatus::ACTIVE,
            'admin_remark' => null,
        ]);

        return $this->refreshProvider($provider);
    }

    /**
     * Ensure Selected User is a Provider
     */
    private function ensureProvider(User $provider): void
    {
        if ($provider->role !== UserRole::PROVIDER) {
            throw ValidationException::withMessages([
                'provider' => [
                    'Selected user is not a provider.',
                ],
            ]);
        }
    }

    /**
     * Reload Provider with Relations
     */
    private function refreshProvider(User $provider): User
    {
        return $provider->fresh([
            'providerProfile',
            'providerProfile.services.category',
            'providerProfile.serviceAreas',
        ]);
    }
}