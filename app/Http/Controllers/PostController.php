<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPostFormRequest;
use App\Http\Requests\PostFormRequest;
use App\Http\Requests\UpdatePostFormRequest;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Image;
use App\Post;
use App\Traits\ImageTrait;
use App\Traits\PostTrait;
use Carbon\Carbon;
use Exception;
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
        $posts = Post::with(['comments', 'images', 'comments.replies'])
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
            $post = Post::create([
                'content' => $request->content,
                'user_id' => $request->user()->id
            ]);

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
     * @return \App\Http\Resources\PostResource
     */
    public function show(GetPostFormRequest $request)
    {
        $post = Post::with(['comments', 'comments.replies', 'images'])
            ->findOrFail($request->postId);

        return response()->baseResponse(
            new PostResource($post)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePostFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePostFormRequest $request)
    {
        $post = Post::with(['images'])
            ->findOrFail($request->postId);

        $postUpdate = DB::transaction(function () use ($request, $post) {
            $post->fill($request->validated());
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
     * @param  int $postId
     * @return \Illuminate\Http\Response
     */
    public function destroy($postId)
    {
        $post = Post::findOrFail($postId);
        $post->delete();

        return response()->baseResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    public function getPostInDate(Request $request, $date)
    {
        $dateParse = Carbon::parse($date);

        $posts = Post::with(['comments', 'images', 'comments.replies'])
            ->whereHas('comments', function ($query) use ($request, $dateParse) {
                return $query->whereUserId($request->user()->id)
                    ->whereDate('created_at', $dateParse);
            })
            ->get();

        return response()->baseResponse(
            $posts
        );
    }
}
