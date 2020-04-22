<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'total' => $this->resource->total(),
            'count' => $this->resource->count(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'next_page_url' => $this->resource->nextPageUrl(),
            'total_pages' => $this->resource->lastPage()
        ];
    }
}
