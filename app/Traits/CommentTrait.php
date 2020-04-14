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
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Comment
     */
    private function getComment($userId, $id)
    {
        $post = Comment::whereUserId($userId)->whereId($id)->first();
        if (!isset($post)) {
            throw new Exception(trans('messages.comment_not_found'), JsonResponse::HTTP_BAD_REQUEST);
        }
        return $post;
    }
}
