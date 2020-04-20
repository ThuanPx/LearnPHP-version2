<?php

namespace App\Traits;

use App\Comment;
use App\User;
use Exception;
use Illuminate\Http\JsonResponse;

trait CommentTrait
{
    /**
     * Get instance comment
     *
     * @param  int $userId
     * @param int $commentId
     * @return \App\Comment
     */
    private function getComment($userId, $commentId)
    {
        $comment = User::findOrFail($userId)->comments()->findOrFail($commentId);

        return $comment;
    }
}
