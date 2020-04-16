<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
use App\Http\Resources\ReplyCommentResource;
use App\Image;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentFormRequest $request)
    {
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = $request->user()->id;

        DB::transaction(function () use ($request, $comment) {
            $post = $this->getPost($request->user()->id, $request->parent_id);
            $post->comments()->save($comment);

            $imageModels = $this->createImageModels($request, $comment);
            if (isset($imageModels)) {
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
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function replyComment(Request $request)
    {
        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = $request->user()->id;

        DB::transaction(function () use ($request, $comment) {
            $commentParent = $this->getComment($request->user()->id, $request->parent_id);
            $commentParent->comments()->save($comment);

            $imageModels = $this->createImageModels($request, $comment);
            if (isset($imageModels)) {
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
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function getReplyComment(Request $request, $parent_id)
    {
        $comments = $this->getComment($request->user()->id, $parent_id)
            ->comments()
            ->orderBy('created_at', 'desc')
            ->get();

        return ReplyCommentResource::collection($comments);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $parent_id)
    {
        $comment = $this->getComment($request->user()->id, $parent_id);
        $comment->content = $request->content;

        DB::transaction(function () use ($request, $comment) {
            $comment->save();
            $comment->images()->delete();

            $imageModels = $this->createImageModels($request, $comment);
            if (isset($imageModels)) {
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
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $parent_id)
    {
        $comment = $this->getComment($request->user()->id, $parent_id);
        $comment->delete();

        return response()->baseResponse([
            'message' => trans('messages.delete_comment_success')
        ]);
    }
}
