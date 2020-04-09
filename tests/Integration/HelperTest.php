<?php namespace Knovators\LaravelModelCaching\Tests\Integration;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class HelperTest extends IntegrationTestCase
{
    public function testClosureRunsWithCacheDisabled()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $authors = app("model-cache")->runDisabled(function () {
            return (new Author)
                ->get();
        });

        $cachedResults1 = $this->cache()
            ->tags($tags)
            ->get($key)["value"]
            ?? null;
        (new Author)
            ->get();
        $cachedResults2 = $this->cache()
            ->tags($tags)
            ->get($key)["value"]
            ?? null;
        $liveResults = (new UncachedAuthor)
            ->get();

        $this->assertEquals($liveResults->toArray(), $authors->toArray());
        $this->assertNull($cachedResults1);
        $this->assertEquals($authors->toArray(), $cachedResults2->toArray());
    }
}
