<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class AdminProviderFilter
{
    public function __construct(
        protected array $filters = []
    ) {
    }

    public function apply(Builder $query): Builder
    {
        $this->filterStatus($query);
        $this->filterSearch($query);

        return $query;
    }

    private function filterStatus(Builder $query): void
    {
        if (empty($this->filters['status'])) {
            return;
        }

        $query->where(
            'status',
            $this->filters['status']
        );
    }

    private function filterSearch(Builder $query): void
    {
        if (empty($this->filters['search'])) {
            return;
        }

        $search = $this->filters['search'];

        $query->where(function ($q) use ($search) {

            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");

        });
    }
}