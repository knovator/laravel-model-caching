<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Supplier;

$factory->define(Supplier::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
