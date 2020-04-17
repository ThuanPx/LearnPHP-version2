<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request)
    {
        $data = $this->resource->only([
            'id',
            'user_id',
            'content',
            'created_at',
            'updated_at',
        ]);

        $data['reply_comments'] = CommentResource::collection(
            $this->whenLoaded('comments')
        );

        $data['images'] = ImageResource::collection(
            $this->whenLoaded('images')
        );

        return $data;
    }
}
