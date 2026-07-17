<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProviderFilter
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function apply(Builder $query): Builder
    {
        $this->filterByCategory($query);
        $this->filterByCity($query);
        $this->filterByPincode($query);
        $this->applySorting($query);

        return $query;
    }

    /**
     * Filter by Category
     */
    private function filterByCategory(Builder $query): void
    {
        if (empty($this->filters['category_id'])) {
            return;
        }

        $query->whereHas('services', function ($q) {
            $q->where(
                'service_category_id',
                $this->filters['category_id']
            );
        });
    }

    /**
     * Filter by City
     */
    private function filterByCity(Builder $query): void
    {
        if (empty($this->filters['city'])) {
            return;
        }

        $query->whereHas('serviceAreas', function ($q) {
            $q->where(
                'city',
                'LIKE',
                '%' . $this->filters['city'] . '%'
            );
        });
    }

    /**
     * Filter by Pincode
     */
    private function filterByPincode(Builder $query): void
    {
        if (empty($this->filters['pincode'])) {
            return;
        }

        $query->whereHas('serviceAreas', function ($q) {
            $q->where(
                'pincode',
                $this->filters['pincode']
            );
        });
    }

    /**
     * Apply Sorting
     */
    private function applySorting(Builder $query): void
    {
        if (empty($this->filters['sort'])) {
            return;
        }

        switch ($this->filters['sort']) {

            case 'rating_desc':
                $query->orderByDesc('average_rating');
                break;

            case 'experience_desc':
                $query->orderByDesc('experience');
                break;

            case 'name_asc':
                $query->join('users', 'users.id', '=', 'provider_profiles.user_id')
                    ->orderBy('users.name')
                    ->select('provider_profiles.*');
                break;

            case 'name_desc':
                $query->join('users', 'users.id', '=', 'provider_profiles.user_id')
                    ->orderByDesc('users.name')
                    ->select('provider_profiles.*');
                break;

            case 'price_low_high':

                if (!empty($this->filters['category_id'])) {

                    $query->join(
                        'provider_services',
                        'provider_services.provider_profile_id',
                        '=',
                        'provider_profiles.id'
                    )
                        ->where(
                            'provider_services.service_category_id',
                            $this->filters['category_id']
                        )
                        ->orderBy('provider_services.base_price')
                        ->select('provider_profiles.*');
                }

                break;

            case 'price_high_low':

                if (!empty($this->filters['category_id'])) {

                    $query->join(
                        'provider_services',
                        'provider_services.provider_profile_id',
                        '=',
                        'provider_profiles.id'
                    )
                        ->where(
                            'provider_services.service_category_id',
                            $this->filters['category_id']
                        )
                        ->orderByDesc('provider_services.base_price')
                        ->select('provider_profiles.*');
                }

                break;
        }
    }
}