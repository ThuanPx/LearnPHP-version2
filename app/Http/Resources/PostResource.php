<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = $this->resource->only([
            'id',
            'user_id',
            'content',
            'created_at',
            'updated_at'
        ]);

        $data['comments'] = CommentResource::collection(
            $this->whenLoaded('comments')
        );

        $data['images'] = ImageResource::collection(
            $this->whenLoaded('images')
        );

        return $data;
    }
}
