<?php

use App\Comment;
use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $user = User::create([
            'name' => $faker->name,
            'email' => $faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'content' => $faker->name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $commentFakers = factory(App\Comment::class, 1)
            ->make([
                'user_id' => 1,
                'commentable_id' => $post->id,
                'commentable_type' => Post::class
            ])
            ->toArray();

        $commentModels = array_map(function ($comment) {
            $comment['created_at'] = now();
            $comment['updated_at'] = now();

            return $comment;
        }, $commentFakers);

        Comment::insert($commentModels);
    }
}
