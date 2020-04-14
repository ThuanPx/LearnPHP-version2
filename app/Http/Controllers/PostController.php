<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Http\Resources\PostResource;
use App\Image;
use App\Post;
use App\Traits\ImageTrait;
use App\Traits\PostTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use PostTrait, ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = $request->user()->posts()->orderBy('created_at', 'desc')->get();
        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostFormRequest $request)
    {
        $post = new Post();
        $post->content = $request->content;
        $post->user_id = $request->user()->id;

        DB::beginTransaction();
        try {
            $post->save();

            $imageModels = $this->createImageModels($request, $post);
            if (isset($imageModels)) Image::insert($imageModels);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.create_post_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $post_id)
    {
        $post = $this->getPost($request->user()->id, $post_id);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostFormRequest $request, $post_id)
    {
        $post = $this->getPost($request->user()->id, $post_id);
        $post->content = $request->content;

        DB::beginTransaction();
        try {
            $post->save();

            $post->images()->delete();
            $imageModels = $this->createImageModels($request, $post);
            if (isset($imageModels)) Image::insert($imageModels);

            // TODO: update images
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return response()->baseResponseStatusCreated([
            'message' => trans('messages.edit_post_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $post_id)
    {
        $post = $this->getPost($request, $post_id);
        $post->delete();

        return response()->baseResponse([
            'message' => trans('messages.delete_post_success')
        ]);
    }
}
