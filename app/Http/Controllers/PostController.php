<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Http\Resources\PostResource;
use App\Image;
use App\Post;
use App\Traits\ImageTrait;
use App\Traits\PostTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    use PostTrait, ImageTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $posts = Post::with(['comments', 'images', 'comments.comments'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('page', 2));

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostFormRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($request, $data) {
            $data['user_id'] = $request->user()->id;
            $post =  Post::create($data);

            $imageModels = $this->createImageModels($request, $post);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
        });

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.create_post_success')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $postId
     * @return \App\Http\Resources\PostResource
     */
    public function show($postId)
    {
        $post = Post::with(['comments','comments.comments', 'images'])->findOrFail($postId);

        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function update(PostFormRequest $request, $postId)
    {
        $post = $this->getPost($request->user()->id, $postId);

        DB::transaction(function () use ($request, $post) {
            $post->content = $request->content;
            $post->save();
            $post->images()->delete();

            $imageModels = $this->createImageModels($request, $post);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }
        });

        return response()->baseResponseStatusCreated([
            'message' => trans('messages.edit_post_success')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int  $postId
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $postId)
    {
        $post = $this->getPost($request->user()->id, $postId);
        $post->delete();

        return response()->baseResponse([
            'message' => trans('messages.delete_post_success')
        ]);
    }
}
