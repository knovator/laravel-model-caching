<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class WithTest extends IntegrationTestCase
{
    public function testWithLimitedQuery()
    {
        $authors = (new Author)
            ->where("id", 1)
            ->with([
                'books' => function ($query) {
                    $query->where("id", "<", 100)
                        ->offset(5)
                        ->limit(1);
                }
            ])
            ->first();
        $uncachedAuthor = (new UncachedAuthor)->with([
                'books' => function ($query) {
                    $query->where("id", "<", 100)
                        ->offset(5)
                        ->limit(1);
                }
            ])
            ->first();

        $this->assertEquals($uncachedAuthor->books()->pluck("id"), $authors->books()->pluck("id"));
        $this->assertEquals($uncachedAuthor->id, $authors->id);
    }

    public function testWithQuery()
    {
        $author = (new Author)
            ->where("id", 1)
            ->with([
                'books' => function ($query) {
                    $query->where("id", "<", 100);
                }
            ])
            ->first();
        $uncachedAuthor = (new UncachedAuthor)->with([
                'books' => function ($query) {
                    $query->where("id", "<", 100);
                },
            ])
            ->where("id", 1)
            ->first();

        $this->assertEquals($uncachedAuthor->books()->count(), $author->books()->count());
        $this->assertEquals($uncachedAuthor->id, $author->id);
    }

    public function testMultiLevelWithQuery()
    {
        $author = (new Author)
            ->where("id", 1)
            ->with([
                'books.publisher' => function ($query) {
                    $query->where("id", "<", 100);
                }
            ])
            ->first();
        $uncachedAuthor = (new UncachedAuthor)->with([
                'books.publisher' => function ($query) {
                    $query->where("id", "<", 100);
                },
            ])
            ->where("id", 1)
            ->first();

        $this->assertEquals($uncachedAuthor->books()->count(), $author->books()->count());
        $this->assertEquals($uncachedAuthor->id, $author->id);
    }

    public function testWithBelongsToManyRelationshipQuery()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-books.id_=_3-testing:{$this->testingSqlitePath}testing.sqlite:stores-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];

        $stores = (new Book)
            ->with("stores")
            ->find(3)
            ->stores;
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ->stores;
        $liveResults = (new UncachedBook)
            ->with("stores")
            ->find(3)
            ->stores;

        $this->assertEquals($liveResults->pluck("id"), $stores->pluck("id"));
        $this->assertEquals($liveResults->pluck("id"), $cachedResults->pluck("id"));
    }
}
