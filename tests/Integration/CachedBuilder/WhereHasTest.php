<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\BookWithUncachedStore;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class WhereHasTest extends IntegrationTestCase
{
    public function testWhereHasClause()
    {
        $authors = (new Author)
            ->whereHas("books")
            ->get();
        $uncachedAuthors = (new UncachedAuthor)
            ->whereHas("books")
            ->get();

        $this->assertEquals($authors->pluck("id"), $uncachedAuthors->pluck("id"));
    }

    public function testNestedWhereHasClauses()
    {
        $authors = (new Author)
            ->where("id", ">", 0)
            ->whereHas("books", function ($query) {
                $query->whereNull("description");
            })
            ->get();
        $uncachedAuthors = (new UncachedAuthor)
            ->where("id", ">", 0)
            ->whereHas("books", function ($query) {
                $query->whereNull("description");
            })
            ->get();

        $this->assertEquals($authors->pluck("id"), $uncachedAuthors->pluck("id"));
    }

    public function testNonCachedRelationshipPreventsCaching()
    {
        $book = (new BookWithUncachedStore)
            ->with("uncachedStores")
            ->whereHas("uncachedStores")
            ->get()
            ->first();
        $store = $book->uncachedStores->first();
        $store->name = "Waterstones";
        $store->save();
        $results = $this->cache()->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesuncachedstore"
            ])
            ->get(sha1(
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-exists-" .
                "and_books.id_=_book_store.book_id-testing:{$this->testingSqlitePath}testing.sqlite:uncachedStores"
            ));

        $this->assertNull($results);
    }
}
