<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BaseCollection extends ResourceCollection
{
    /**
     * Create data collection
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator          $data
     * @return array
     */
    protected function createCustomDataCollection($data)
    {
        return [
            'data' => $data,
            'total' => $this->resource->total(),
            'count' => $this->resource->count(),
            'per_page' => $this->resource->perPage(),
            'current_page' => $this->resource->currentPage(),
            'next_page_url' => $this->resource->nextPageUrl(),
            'total_pages' => $this->resource->lastPage()
        ];
    }

    /**
     * get data
     *
     * @return mixed
     */
    protected function getCollection()
    {
        $resourceClassName = str_replace('Collection', 'Resource', get_class($this));
        $result = $resourceClassName::collection($this->resource->getCollection());

        return $result;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request)
    {
        // if resource is paginable
        if ($this->resource instanceof LengthAwarePaginator) {
            return $this->createCustomDataCollection(
                $this->getCollection()
            );
        }

        return $this->getCollection();
    }
}
