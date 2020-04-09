<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedUser;
use Knovators\LaravelModelCaching\Tests\Fixtures\User;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class PolymorphicOneToOneTest extends IntegrationTestCase
{
    public function testEagerloadedRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:images:knovatorslaravelmodelcachingtestsfixturesimage-images.imagable_id_inraw_2-images.imagable_type_=_Knovators\LaravelModelCaching\Tests\Fixtures\User");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesimage",
        ];

        $result = (new User)
            ->with("image")
            ->whereHas("image")
            ->first()
            ->image;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ->first();
        $liveResults = (new UncachedUser)
            ->with("image")
            ->whereHas("image")
            ->first()
            ->image;

        $this->assertEquals($liveResults->path, $result->path);
        $this->assertEquals($liveResults->path, $cachedResults->path);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testLazyloadedHasOneThrough()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:images:knovatorslaravelmodelcachingtestsfixturesimage-images.imagable_id_=_2-images.imagable_id_notnull-images.imagable_type_=_Knovators\LaravelModelCaching\Tests\Fixtures\User-limit_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesimage",
        ];

        $result = (new User)
            ->whereHas("image")
            ->first()
            ->image;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ->first();
        $liveResults = (new UncachedUser)
            ->whereHas("image")
            ->first()
            ->image;

        $this->assertEquals($liveResults->path, $result->path);
        $this->assertEquals($liveResults->path, $cachedResults->path);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }
}
