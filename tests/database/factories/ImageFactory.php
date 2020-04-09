<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Image;

$factory->define(Image::class, function (Faker $faker) {
    return [
        'path' => $faker->imageUrl(),
    ];
});
