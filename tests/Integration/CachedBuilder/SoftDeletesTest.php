<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class SoftDeletesTest extends IntegrationTestCase
{
    public function testWithTrashedIsCached()
    {
        $author = (new UncachedAuthor)
            ->first();
        $author->delete();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-find_1-withTrashed");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $deletedAuthor = (new Author)
            ->withTrashed()
            ->find($author->id);
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $deletedUncachedAuthor = (new UncachedAuthor)
            ->withTrashed()
            ->find($author->id);

        $this->assertEquals($cachedResults->toArray(), $deletedAuthor->toArray());
        $this->assertEquals($cachedResults->toArray(), $deletedUncachedAuthor->toArray());
    }

    public function testWithoutTrashedIsCached()
    {
        $author = (new UncachedAuthor)
            ->first();
        $author->delete();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-find_{$author->id}-withoutTrashed");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $result = (new Author)
            ->withoutTrashed()
            ->find($author->id);
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $uncachedResult = (new UncachedAuthor)
            ->withoutTrashed()
            ->find($author->id);

        $this->assertEquals($uncachedResult, $result);
        $this->assertEquals($uncachedResult, $cachedResult);
        $this->assertNull($result);
        $this->assertNull($cachedResult);
        $this->assertNull($uncachedResult);
    }

    public function testonlyTrashedIsCached()
    {
        $author = (new UncachedAuthor)
            ->first();
        $author->delete();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_notnull-find_{$author->id}-onlyTrashed");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $deletedAuthor = (new Author)
            ->onlyTrashed()
            ->find($author->id);
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $deletedUncachedAuthor = (new UncachedAuthor)
            ->onlyTrashed()
            ->find($author->id);

        $this->assertEquals($cachedResults->toArray(), $deletedAuthor->toArray());
        $this->assertEquals($cachedResults->toArray(), $deletedUncachedAuthor->toArray());
    }
}
