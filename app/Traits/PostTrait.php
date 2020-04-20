<?php

namespace App\Traits;

use App\Post;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;

trait PostTrait
{
    /**
     * Get instance post
     *
     * @param  int $userId
     * @param int $postId
     * @return \App\Post
     */
    private function getPost($userId, $postId)
    {
        $post = User::findOrFail($userId)->posts()->findOrFail($postId);

        return $post;
    }
}
