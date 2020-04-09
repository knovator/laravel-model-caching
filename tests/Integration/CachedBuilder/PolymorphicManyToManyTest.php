<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Post;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPost;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class PolymorphicManyToManyTest extends IntegrationTestCase
{
    public function testEagerloadedRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:posts:knovatorslaravelmodelcachingtestsfixturespost-testing:{$this->testingSqlitePath}testing.sqlite:tags-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturespost",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturestag",
        ];

        $result = (new Post)
            ->with("tags")
            ->first()
            ->tags;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedPost)
            ->with("tags")
            ->first()
            ->tags;

        $this->assertEquals($liveResults->pluck("id")->toArray(), $result->pluck("id")->toArray());
        $this->assertEquals($liveResults->pluck("id")->toArray(), $cachedResults->pluck("id")->toArray());
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testLazyloadedRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:posts:knovatorslaravelmodelcachingtestsfixturespost-testing:{$this->testingSqlitePath}testing.sqlite:tags-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturespost",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturestag",
        ];

        // $result = (new Post)
        //     ->with("tags")
        //     ->first()
        //     ->tags;
        // $cachedResults = $this->cache()
        //     ->tags($tags)
        //     ->get($key)['value'];
        // $liveResults = (new UncachedPost)
        //     ->with("tags")
        //     ->first()
        //     ->tags;

        // $this->assertEquals($liveResults->pluck("id")->toArray(), $result->pluck("id")->toArray());
        // $this->assertEquals($liveResults->pluck("id")->toArray(), $cachedResults->pluck("id")->toArray());
        // $this->assertNotEmpty($result);
        // $this->assertNotEmpty($cachedResults);
        // $this->assertNotEmpty($liveResults);
        $this->markTestSkipped();
    }
}
