<?php

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\Printer;

$factory->define(Printer::class, function (Faker $faker) {
    return [
        "book_id" => factory(Book::class)->create()->id,
        'name' => $faker->realText(),
    ];
});
