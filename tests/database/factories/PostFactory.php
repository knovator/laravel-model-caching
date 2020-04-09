<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Post;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'body' => $faker->paragraph,
    ];
});
