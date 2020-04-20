<?php

use App\Post;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
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

        $dataPostFakers = factory(App\Post::class, 1)
            ->make(['user_id' => $user->id])
            ->toArray();

        $dataPostModels = array_map(function ($post) {
            $post['created_at'] = now();
            $post['updated_at'] = now();

            return $post;
        }, $dataPostFakers);

        Post::insert($dataPostModels);
    }
}
