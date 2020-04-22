<?php

namespace App\Traits;

use App\Comment;

trait CommentTrait
{
    /**
     * Get instance comment
     *
     * @param  int $userId
     * @return \App\Comment
     */
    private function getComment($userId)
    {
        $comment = Comment::whereUserId($userId);

        return $comment;
    }
}
