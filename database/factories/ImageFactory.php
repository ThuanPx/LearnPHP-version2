<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Storage;

$factory->define(Image::class, function (Faker $faker) {
    $image = array_rand(Storage::allFiles('images'));
    return [
        'url' => storage_path('app/' . $image),
        'imageable_id' => App\Post::all()->random()->id,
        'imageable_type' => App\Post::class
    ];
});
