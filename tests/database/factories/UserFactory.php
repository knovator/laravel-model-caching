<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Supplier;
use Knovators\LaravelModelCaching\Tests\Fixtures\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        "supplier_id" => factory(Supplier::class)->create()->id,
    ];
});
