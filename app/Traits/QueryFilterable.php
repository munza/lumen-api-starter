<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

trait QueryFilterable
{
    /**
     * Scope a query for query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        $query = new QueryBuilder($this->query(), $request);

        switch (true) {
            case property_exists($this, 'filterable'):
                $query = $query->allowedFilters($this->filterable);

            case property_exists($this, 'sortable'):
                $query = $query->allowedSorts($this->sortable);

            case property_exists($this, 'includable'):
                $query = $query->allowedIncludes($this->includable);

            case property_exists($this, 'visible'):
                $query = $query->allowedFields($this->visible);

            case property_exists($this, 'appendable'):
                $query = $query->allowedAppends($this->appendable);
        }

        return $query;
    }
}
