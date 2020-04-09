<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Tag;

$factory->define(Tag::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
    ];
});
