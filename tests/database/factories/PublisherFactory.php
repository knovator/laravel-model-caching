<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Publisher;

$factory->define(Publisher::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
