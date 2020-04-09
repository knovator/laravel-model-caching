<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Supplier;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedSupplier;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class HasOneThroughTest extends IntegrationTestCase
{
    public function testEagerloadedHasOneThrough()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:suppliers:knovatorslaravelmodelcachingtestsfixturessupplier-testing:{$this->testingSqlitePath}testing.sqlite:history-limit_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturessupplier",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixtureshistory",
        ];

        $history = (new Supplier)
            ->with("history")
            ->first()
            ->history;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ->first()
            ->history;
        $liveResults = (new UncachedSupplier)
            ->with("history")
            ->first()
            ->history;

        $this->assertEquals($liveResults->id, $history->id);
        $this->assertEquals($liveResults->id, $cachedResults->id);
        $this->assertNotEmpty($history);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testLazyloadedHasOneThrough()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:suppliers:knovatorslaravelmodelcachingtestsfixturessupplier-testing:{$this->testingSqlitePath}testing.sqlite:history-limit_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturessupplier",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixtureshistory",
        ];

        // $history = (new Supplier)
        //     ->first()
        //     ->history;
        // $cachedResults = $this->cache()
        //     ->tags($tags)
        //     ->get($key)['value'];
        // $liveResults = (new UncachedSupplier)
        //     ->first()
        //     ->history;

        // $this->assertEquals($liveResults->id, $history->id);
        // $this->assertEquals($liveResults->id, $cachedResults->id);
        // $this->assertNotEmpty($history);
        // $this->assertNotEmpty($cachedResults);
        // $this->assertNotEmpty($liveResults);
        $this->markTestSkipped();
    }
}
