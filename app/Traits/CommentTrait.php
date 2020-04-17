<?php

namespace App\Traits;

use App\Comment;
use Exception;
use Illuminate\Http\JsonResponse;

trait CommentTrait
{
    /**
     * Get instance comment
     *
     * @param  int $userId
     * @param int $id
     * @return \App\Comment
     */
    private function getComment($userId, $id)
    {
        $comment = Comment::whereUserId($userId)->whereId($id)->first();
        if (!isset($comment)) {
            throw new Exception(trans('messages.comment_not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }

        return $comment;
    }
}
