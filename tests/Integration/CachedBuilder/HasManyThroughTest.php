<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class HasManyThroughTest extends IntegrationTestCase
{
    public function testEagerloadedHasManyThrough()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-testing:{$this->testingSqlitePath}testing.sqlite:printers-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesprinter",
        ];

        $printers = (new Author)
            ->with("printers")
            ->first()
            ->printers;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ->first()
            ->printers;
        $liveResults = (new UncachedAuthor)
            ->with("printers")
            ->first()
            ->printers;

        $this->assertEquals($liveResults->pluck("id")->toArray(), $printers->pluck("id")->toArray());
        $this->assertEquals($liveResults->pluck("id")->toArray(), $cachedResults->pluck("id")->toArray());
        $this->assertNotEmpty($printers);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testLazyloadedHasManyThrough()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-authors.id_=_1-testing:{$this->testingSqlitePath}testing.sqlite:printers-limit_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesprinter",
        ];

        // $printers = (new Author)
        //     ->find(1)
        //     ->printers;
        // $cachedResults = $this->cache()
        //     ->tags($tags)
        //     ->get($key)['value']
        //     ->first()
        //     ->printers;
        // $liveResults = (new UncachedAuthor)
        //     ->find(1)
        //     ->printers;

        // $this->assertEquals($liveResults->pluck("id")->toArray(), $printers->pluck("id")->toArray());
        // $this->assertEquals($liveResults->pluck("id")->toArray(), $cachedResults->pluck("id")->toArray());
        // $this->assertNotEmpty($printers);
        // $this->assertNotEmpty($cachedResults);
        // $this->assertNotEmpty($liveResults);
        $this->markTestSkipped();
    }
}
