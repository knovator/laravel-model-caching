<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Knovators\LaravelModelCaching\Tests\Fixtures\PrefixedAuthor;

class PrefixTest extends IntegrationTestCase
{
    public function testCachePrefixIsAddedForPrefixedModel()
    {
        $prefixKey = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:model-prefix:authors:knovatorslaravelmodelcachingtestsfixturesprefixedauthor-authors.deleted_at_null-first");
        $prefixTags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:model-prefix:knovatorslaravelmodelcachingtestsfixturesprefixedauthor",
        ];
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $prefixAuthor = (new PrefixedAuthor)
            ->first();
        $author = (new Author)
            ->first();
        $prefixCachedResults = $this
            ->cache()
            ->tags($prefixTags)
            ->get($prefixKey)['value'];
        $nonPrefixCachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];

        $this->assertEquals($prefixCachedResults, $prefixAuthor);
        $this->assertEquals($nonPrefixCachedResults, $author);
        $this->assertNotNull($author);
    }
}
