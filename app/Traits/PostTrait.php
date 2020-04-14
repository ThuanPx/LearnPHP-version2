<?php

namespace App\Traits;

use App\Post;
use Exception;
use Illuminate\Http\JsonResponse;

trait PostTrait
{
    /**
     * Get instance post
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Post
     */
    private function getPost($userId, $id)
    {
        $post = Post::whereUserId($userId)->whereId($id)->first();
        if (!isset($post)) {
            throw new Exception(trans('messages.post_not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }
        return $post;
    }
}
