<?php namespace Knovators\LaravelModelCaching\Tests\Integration;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class CachedBuilderRelationshipsTest extends IntegrationTestCase
{
    public function testHasRelationshipResults()
    {
        $booksWithStores = (new Book)
            ->with("stores")
            ->has("stores")
            ->get();
        $key = "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-exists-and_books.id_=_book_store.book_id-testing:{$this->testingSqlitePath}testing.sqlite:stores";
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get(sha1($key))["value"];

        $this->assertNotEmpty($booksWithStores);
        $this->assertEquals($booksWithStores, $cachedResults);
    }

    public function testWhereHasRelationship()
    {
        $books = (new Book)
            ->with("stores")
            ->whereHas("stores", function ($query) {
                $query->whereRaw('address like ?', ['%s%']);
            })
            ->get();

        $uncachedBooks = (new UncachedBook)
            ->with("stores")
            ->whereHas("stores", function ($query) {
                $query->whereRaw('address like ?', ['%s%']);
            })
            ->get();

        $this->assertEquals($books->pluck("id"), $uncachedBooks->pluck("id"));
    }
}
