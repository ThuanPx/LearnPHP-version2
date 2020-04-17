<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
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
     * @param  \App\Http\Requests\CommentFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentFormRequest $request)
    {
        Post::findOrFail($request->postId);

        $comment = DB::transaction(function () use ($request) {
            $comment = new Comment($request->validated());
            $comment['user_id'] = $request->user()->id;
            $comment['commentable_id'] = $request->postId;
            $comment['commentable_type'] = Post::class;
            $comment->save();

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
     * @param  \App\Http\Requests\CommentFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function replyComment(CommentFormRequest $request)
    {
        Comment::findOrFail($request->commentId);

        $comment = DB::transaction(function () use ($request) {
            $comment = new Comment($request->validated());
            $comment['user_id'] = $request->user()->id;
            $comment['commentable_id'] = $request->commentId;
            $comment['commentable_type'] = Comment::class;
            $comment->save();

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
            ->get();

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
            $comment['content'] = $request->content;
            $comment->save();
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
