<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class FindOrFailTest extends IntegrationTestCase
{
    public function testFindOrFailCachesModels()
    {
        $author = (new Author)
            ->findOrFail(1);

        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-find_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->findOrFail(1);

        $this->assertEquals($cachedResults->toArray(), $author->toArray());
        $this->assertEquals($liveResults->toArray(), $author->toArray());
    }

    public function testFindOrFailWithArrayReturnsResults()
    {
        $author = (new Author)->findOrFail([1, 2]);
        $uncachedAuthor = (new UncachedAuthor)->findOrFail([1, 2]);

        $this->assertEquals($uncachedAuthor->count(), $author->count());
        $this->assertEquals($uncachedAuthor->pluck("id"), $author->pluck("id"));
    }
}
