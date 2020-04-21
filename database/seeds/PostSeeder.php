<?php

use App\Comment;
use App\Image;
use App\Post;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Post::class, 3)->create()->each(function ($post) {
            Comment::insert(
                factory(App\Comment::class, 5)
                    ->make([
                        'user_id' => $post->user_id,
                        'commentable_id' => $post->id
                    ])
                    ->toArray()
            );
            Image::insert(
                factory(App\Image::class, 5)
                    ->make([
                        'imageable_id' => $post->id,
                        'imageable_type' => Post::class
                    ])
                    ->toArray()
            );
        });
    }
}
