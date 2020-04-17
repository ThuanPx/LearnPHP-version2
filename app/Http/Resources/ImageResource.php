<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->only([
            'id',
            'url',
            'created_at',
            'updated_at',
        ]);
    }
}
