<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Author;

$factory->define(Author::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        "finances" => [
            "total" => 5000,
            "weekly" => 100,
            "daily" => 20,
        ],
        "is_famous" => $faker->boolean(),
    ];
});
