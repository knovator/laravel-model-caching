<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Store;

$factory->define(Store::class, function (Faker $faker) {
    return [
        'address' => $faker->address,
        'name' => $faker->company,
    ];
});
