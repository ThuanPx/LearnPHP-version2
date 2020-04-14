<?php

namespace App\Http\Controllers;

use App\Comment;
use App\Http\Requests\CommentFormRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\ReplyCommentResource;
use App\Image;
use App\Traits\CommentTrait;
use App\Traits\PostTrait;
use Illuminate\Http\Request;
use App\Traits\ImageTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    use PostTrait, ImageTrait, CommentTrait;

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentFormRequest $request)
    {

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = $request->user()->id;

        DB::beginTransaction();
        try {
            // Check type of commentable
            if ($request->type == 'post') {
                $post = $this->getPost($request->user()->id, $request->parent_id);
                $post->comments()->save($comment);
            } elseif ($request->type == 'comment') {
                $commentParent = $this->getComment($request->user()->id, $request->parent_id);
                $commentParent->comments()->save($comment);
            }

            $imageModels = $this->createImageModels($request, $comment);
            if (isset($imageModels)) Image::insert($imageModels);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.create_comment_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $parent_id)
    {
        $comments = $this->getPost($request->user()->id, $parent_id)->comments()->orderBy('created_at', 'desc')->get();

        return CommentResource::collection($comments);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $parent_id
     * @return \Illuminate\Http\Response
     */
    public function replyComment(Request $request, $parent_id)
    {
        $comments = $this->getComment($request->user()->id, $parent_id)->comments()->orderBy('created_at', 'desc')->get();

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

        dd($request->file('images'));
        DB::beginTransaction();
        try {
            $comment->save();

            $comment->images()->delete();
            $imageModels = $this->createImageModels($request, $comment);
            if (isset($imageModels)) Image::insert($imageModels);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

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
