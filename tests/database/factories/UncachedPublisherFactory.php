<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPublisher;

$factory->define(UncachedPublisher::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
