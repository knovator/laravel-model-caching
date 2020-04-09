<?php namespace Knovators\LaravelModelCaching\Tests\Integration\Traits;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class CachePrefixingTest extends IntegrationTestCase
{
    public function testDatabaseKeyingEnabled()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $author = (new Author)
            ->first();
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->first();

        $this->assertEquals($liveResults->pluck("id"), $author->pluck("id"));
        $this->assertEquals($liveResults->pluck("id"), $cachedResults->pluck("id"));
        $this->assertNotEmpty($author);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testDatabaseKeyingDisabled()
    {
        config(["laravel-model-caching.use-database-keying" => false]);
        $key = sha1("knovators:laravel-model-caching:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-first");
        $tags = ["knovators:laravel-model-caching:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $author = (new Author)
            ->first();
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->first();

        $this->assertEquals($liveResults->pluck("id"), $author->pluck("id"));
        $this->assertEquals($liveResults->pluck("id"), $cachedResults->pluck("id"));
        $this->assertNotEmpty($author);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }
}
