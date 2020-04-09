<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class WhereNullTest extends IntegrationTestCase
{
    public function testWhereNullClause()
    {
        $books = (new Book)
            ->whereNull("description")
            ->get();
        $uncachedBooks = (new UncachedBook)
            ->whereNull("description")
            ->get();

        $this->assertEquals($books->pluck("id"), $uncachedBooks->pluck("id"));
    }

    public function testNestedWhereNullClauses()
    {
        $books = (new Book)
            ->where(function ($query) {
                $query->whereNull("description");
            })
            ->get();
        $uncachedBooks = (new UncachedBook)
            ->where(function ($query) {
                $query->whereNull("description");
            })
            ->get();

        $this->assertEquals($books->pluck("id"), $uncachedBooks->pluck("id"));
    }
}
