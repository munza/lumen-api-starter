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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        (new QueryBuilder($this->query(), $request))
            ->allowedFilters($this->filterable ?? [])
            ->allowedSorts($this->sortable ?? [])
            ->allowedFields($this->visible ?? [])
            ->allowedIncludes($this->includable ?? [])
            ->allowedAppends($this->appendable ?? []);

        return $query;
    }
}
