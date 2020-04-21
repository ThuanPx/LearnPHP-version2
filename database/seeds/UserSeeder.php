<?php

use App\Post;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 3)->create()->each(function ($user) {
            Post::insert(
                factory(App\Post::class, 5)->make(['user_id' => $user->id])->toArray()
            );
        });
     }
}
