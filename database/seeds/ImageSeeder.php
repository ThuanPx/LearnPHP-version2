<?php

use App\Image;
use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ImageSeeder extends Seeder
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

        $imageFakers = factory(App\Image::class, 1)
            ->make([
                'imageable_id' => $post->id,
                'imageable_type' => Post::class,
            ])
            ->toArray();

        $imageModels = array_map(function ($image) {
            $image['created_at'] = now();
            $image['updated_at'] = now();
            return $image;
        }, $imageFakers);

        Image::insert($imageModels);
    }
}
