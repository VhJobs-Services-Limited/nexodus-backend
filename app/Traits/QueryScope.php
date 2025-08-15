<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

trait QueryScope
{
    /**
     * paginate the records when the paginate parameter
     * is present in the request
     */
    public function scopeWithPagination(Builder $query, Request $request): Collection|Paginator
    {
        return $query->when(
            $request->input('paginate') !== 'false',
            fn (Builder $query) => $query->simplePaginate($request->get('per_page', 10))->withQueryString(),
            fn (Builder $query) => $query->get()
        );
    }

    /**
     * limit the number of records to be returned
     * when the limit parameter is present in the request
     */
    public function scopeWithLimit(Builder $query, Request $request): Builder
    {
        return $query->when(
            $request->has('limit'),
            fn (Builder $query) => $query->limit($request->get('limit', 10)),
        );
    }
}
