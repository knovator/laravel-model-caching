<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class FindTest extends IntegrationTestCase
{
    public function testFindModelResultsCreatesCache()
    {
        $author = collect()->push((new Author)->find(1));
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = collect()->push($this->cache()->tags($tags)
            ->get($key));
        $liveResults = collect()->push((new UncachedAuthor)->find(1));

        $this->assertEmpty($author->diffKeys($cachedResults));
        $this->assertEmpty($liveResults->diffKeys($cachedResults));
    }

    public function testFindMultipleModelResultsCreatesCache()
    {
        $authors = (new Author)
            ->find([1, 2, 3]);
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-find_list_1_2_3");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)["value"];
        $liveResults = (new UncachedAuthor)->find([1, 2, 3]);

        $this->assertEquals($authors->pluck("id"), $cachedResults->pluck("id"));
        $this->assertEquals($liveResults->pluck("id"), $cachedResults->pluck("id"));
    }

    public function testSubsequentFindsReturnDifferentModels()
    {
        $author1 = (new Author)->find(1);
        $author2 = (new Author)->find(2);

        $this->assertNotEquals($author1, $author2);
        $this->assertEquals($author1->id, 1);
        $this->assertEquals($author2->id, 2);
    }

    public function testFindWithArrayReturnsResults()
    {
        $author = (new Author)->find([1, 2]);
        $uncachedAuthor = (new UncachedAuthor)->find([1, 2]);

        $this->assertEquals($uncachedAuthor->count(), $author->count());
        $this->assertEquals($uncachedAuthor->pluck("id"), $author->pluck("id"));
    }

    public function testFindWithSingleElementArrayDoesntConflictWithNormalFind()
    {
        $author1 = (new Author)
            ->find(1);
        $author2 = (new Author)
            ->find([1]);
        
        $this->assertNotEquals($author1, $author2);
        $this->assertIsIterable($author2);
        $this->assertEquals(Author::class, get_class($author1));
    }
}
