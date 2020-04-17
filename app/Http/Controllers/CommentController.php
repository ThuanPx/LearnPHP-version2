<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
use App\Http\Resources\CommentResource;
use App\Image;
use App\Post;
use App\Traits\CommentTrait;
use App\Traits\PostTrait;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
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

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['commentable_id'] = $request->postId;
        $data['commentable_type'] = Post::class;

        DB::transaction(function () use ($request, $data) {
            $comment = Comment::create($data);

            $imageModels = $this->createImageModels($request, $comment);

            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
        });

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.create_comment_success')
        ]);
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

        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['commentable_id'] = $request->commentId;
        $data['commentable_type'] = Comment::class;

        DB::transaction(function () use ($request, $data) {
            $comment = Comment::create($data);

            $imageModels = $this->createImageModels($request, $comment);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
        });

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.create_comment_success')
        ]);
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

        return CommentResource::collection($comments);
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

        DB::transaction(function () use ($request, $comment) {
            $comment->content = $request->content;
            $comment->save();
            $comment->images()->delete();

            $imageModels = $this->createImageModels($request, $comment);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
        });

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.update_comment_success')
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

        return response()->baseResponse([
            'message' => trans('messages.delete_comment_success')
        ]);
    }
}
