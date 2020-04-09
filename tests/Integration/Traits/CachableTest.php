<?php namespace Knovators\LaravelModelCaching\Tests\Integration\Traits;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\PrefixedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\Profile;
use Knovators\LaravelModelCaching\Tests\Fixtures\Publisher;
use Knovators\LaravelModelCaching\Tests\Fixtures\Store;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedProfile;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPublisher;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedStore;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Collection;

class CachableTest extends IntegrationTestCase
{
    public function testSpecifyingAlternateCacheDriver()
    {
        $configCacheStores = config('cache.stores');
        $configCacheStores['customCache'] = ['driver' => 'array'];
        // TODO: make sure the alternate cache is actually loaded
        config(['cache.stores' => $configCacheStores]);
        config(['laravel-model-caching.store' => 'customCache']);
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $authors = (new Author)
            ->all();
        $defaultcacheResults = app('cache')
            ->tags($tags)
            ->get($key)['value']
            ?? null;
        $customCacheResults = app('cache')
            ->store('customCache')
            ->tags($tags)
            ->get($key)['value']
            ?? null;
        $liveResults = (new UncachedAuthor)
            ->all();

        $this->assertEquals($customCacheResults, $authors);
        $this->assertNull($defaultcacheResults);
        $this->assertEmpty($liveResults->diffAssoc($customCacheResults));
    }

    public function testSetCachePrefixAttribute()
    {
        (new PrefixedAuthor)->get();

        $results = $this->
            cache()
            ->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:model-prefix:knovatorslaravelmodelcachingtestsfixturesprefixedauthor",
            ])
            ->get(sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:model-prefix:authors:knovatorslaravelmodelcachingtestsfixturesprefixedauthor-authors.deleted_at_null"))['value'];

        $this->assertNotNull($results);
    }

    public function testAllReturnsCollection()
    {
        (new Author)->truncate();
        factory(Author::class, 1)->create();
        $authors = (new Author)->all();

        $cachedResults = $this
            ->cache()
            ->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            ])
            ->get(sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null"))['value'];
        $liveResults = (new UncachedAuthor)->all();

        $this->assertInstanceOf(Collection::class, $authors);
        $this->assertInstanceOf(Collection::class, $cachedResults);
        $this->assertInstanceOf(Collection::class, $liveResults);
    }

    public function testsCacheFlagDisablesCaching()
    {
        config(['laravel-model-caching.enabled' => false]);

        $authors = (new Author)->get();
        $cachedAuthors = $this
            ->cache()
            ->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            ])
            ->get(sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null"));

        config(['laravel-model-caching.enabled' => true]);

        $this->assertNull($cachedAuthors);
        $this->assertNotEmpty($authors);
        $this->assertCount(10, $authors);
    }
}
