<?php namespace Knovators\LaravelModelCaching\Tests\Integration\Traits;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Illuminate\Database\Eloquent\Collection;

class BuilderCachingTest extends IntegrationTestCase
{
    public function testDisablingAllQuery()
    {
        $allAuthors = (new Author)
            ->disableCache()
            ->all();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];
        $cachedAuthors = $this
            ->cache()
            ->tags($tags)
            ->get($key)["value"]
            ?? null;

        $this->assertInstanceOf(Collection::class, $allAuthors);
        $this->assertNull($cachedAuthors);
    }
}
