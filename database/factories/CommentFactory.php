<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comment;
use Faker\Generator as Faker;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\User::class),
        'content' => $faker->address,
        'commentable_id' => factory(App\Post::class),
        'commentable_type' => App\Post::class
    ];
});