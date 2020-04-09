<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\History;
use Knovators\LaravelModelCaching\Tests\Fixtures\User;

$factory->define(History::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        "user_id" => factory(User::class)->create()->id,
    ];
});
