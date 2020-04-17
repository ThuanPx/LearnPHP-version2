<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Image;
use App\Post;
use App\Traits\ImageTrait;
use App\Traits\PostTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
    public function index()
    {
        $posts = Post::with(['comments', 'images', 'comments.comments'])
            ->orderBy('created_at', 'desc')
            ->paginate(2);

        return response()->baseResponse(
            new PostCollection($posts)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\PostFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostFormRequest $request)
    {
        $post = DB::transaction(function () use ($request) {
            $post = new Post($request->validated());
            $post['user_id'] = $request->user()->id;
            $post->save();

            $imageModels = $this->createImageModels($request, $post);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }

            return $post;
        });

        return response()->baseResponseStatusCreated(
            new PostResource($post)
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $postId
     * @return \App\Http\Resources\PostResource
     */
    public function show($postId)
    {
        $post = Post::with(['comments', 'comments.comments', 'images'])
            ->findOrFail($postId);

        return response()->baseResponse(
            new PostResource($post)
        );
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

        $postUpdate = DB::transaction(function () use ($request, $post) {
            $post->content = $request->content;
            $post->save();
            $post->images()->delete();

            $imageModels = $this->createImageModels($request, $post);
            if (!empty($imageModels)) {
                Image::insert($imageModels);
            }

            return $post;
        });

        return response()->baseResponseStatusCreated(
            new PostResource($postUpdate)
        );
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

        return response()->baseResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function getPostInDate(Request $request, $date)
    {
        $dateParse = Carbon::parse($date);

        $posts = Post::with(['comments', 'images', 'comments.comments'])
            ->whereHas('comments', function ($query) use ($request, $dateParse) {
                return $query->whereUserId($request->user()->id)
                    ->whereDate('created_at', $dateParse);
            })
            ->get();

        return response()->baseResponse([
            'data' =>  PostResource::collection($posts)
        ]);
    }
}
