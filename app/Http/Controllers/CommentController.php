<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
use App\Http\Requests\CreateCommentFormRequest;
use App\Http\Requests\CreateReplyCommentFormRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Image;
use App\Post;
use App\Traits\CommentTrait;
use App\Traits\PostTrait;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use PostTrait, ImageTrait, CommentTrait;

    /**
     * Create comment
     *
     * @param  \App\Http\Requests\CreateCommentFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCommentFormRequest $request)
    {
        $comment = DB::transaction(function () use ($request) {
            $comment = Comment::create(
                [
                    'content' => $request->content,
                    'user_id' => $request->user()->id,
                    'commentable_id' => $request->postId,
                    'commentable_type' => Post::class,
                ]
            );

            $imageModels = $this->createImageModels($request, $comment);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }

            return $comment;
        });

        return response()->baseResponseStatusCreated(
            new CommentResource($comment)
        );
    }

    /**
     * Create reply comment
     *
     * @param  \App\Http\Requests\CreateReplyCommentFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function replyComment(CreateReplyCommentFormRequest $request)
    {
        $comment = DB::transaction(function () use ($request) {
            $comment = Comment::create(
                [
                    'content' => $request->content,
                    'user_id' => $request->user()->id,
                    'commentable_id' => $request->commentId,
                    'commentable_type' => Comment::class,
                ]
            );

            $imageModels = $this->createImageModels($request, $comment);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
            return $comment;
        });

        return response()->baseResponseStatusCreated(
            new CommentResource($comment)
        );
    }

    /**
     * Get all reply comment
     *
     * @param  int  $commentId
     * @return \Illuminate\Http\Resources\Json\JsonResource::collection
     */
    public function getReplyComment($commentId)
    {
        $comments = Comment::with('images')
            ->findOrFail($commentId)
            ->comments()
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return response()->baseResponseStatusCreated([
            new CommentCollection($comments)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentFormRequest  $request
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    public function update(CommentFormRequest $request, $commentId)
    {
        $comment = $this->getComment($request->user()->id, $commentId);

        $commentUpdate =  DB::transaction(function () use ($request, $comment) {
            $comment->fill(['content' => $request->content]);
            $comment->images()->delete();

            $imageModels = $this->createImageModels($request, $comment);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
            return $comment;
        });

        return response()->baseResponseStatusCreated([
            new CommentResource($commentUpdate)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $parentId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $parentId)
    {
        $comment = $this->getComment($request->user()->id, $parentId);
        $comment->delete();

        return response()->baseResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
