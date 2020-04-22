<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
use App\Http\Requests\CreateCommentFormRequest;
use App\Http\Requests\CreateReplyCommentFormRequest;
use App\Http\Requests\GetRepliesFormRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Image;
use App\Post;
use App\Traits\CommentTrait;
use App\Traits\PostTrait;
use App\Traits\ImageTrait;
use Composer\DependencyResolver\Request;
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
     * @param  \App\Http\Requests\GetRepliesFormRequest  $request
     * @return \Illuminate\Http\Resources\Json\JsonResource::collection
     */
    public function getRepliesOfComment(GetRepliesFormRequest $request)
    {
        $comments = Comment::with('images')
            ->whereCommentableId($request->commentId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->baseResponseStatusCreated([
            new CommentCollection($comments)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CommentFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CommentFormRequest $request)
    {
        $comment = Comment::with(['images'])->findOrFail($request->commentId);

        $commentUpdate =  DB::transaction(function () use ($request, $comment) {
            $comment->fill($request->validated());
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
     * @param  int  $commentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();

        return response()->baseResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
