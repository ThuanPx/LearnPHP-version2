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
        if (!isset($comment)) {
            throw new Exception(trans('messages.comment_not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $comment;
    }
}
