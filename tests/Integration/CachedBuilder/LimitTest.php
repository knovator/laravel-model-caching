<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class LimitTest extends IntegrationTestCase
{
    public function testGetModelResultsCreatesCache()
    {
        $authors = (new Author)
            ->limit(5)
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:/Users/mike/Developer/Sites/laravel-model-caching/tests/database/testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-limit_5");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->limit(5)
            ->get();

        $this->assertEquals($authors, $cachedResults);
        $this->assertEmpty($liveResults->diffKeys($cachedResults));
    }
}
