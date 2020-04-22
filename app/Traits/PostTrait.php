<?php

namespace App\Traits;

use App\Post;

trait PostTrait
{
    /**
     * Get instance post
     *
     * @param  int $postId
     * @return \App\Post
     */
    private function getPost($postId)
    {
        $post = Post::findOrFail($postId);

        return $post;
    }
}
