<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Post;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPost;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class PolymorphicOneToManyTest extends IntegrationTestCase
{
    public function testEagerloadedRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:comments:knovatorslaravelmodelcachingtestsfixturescomment-comments.commentable_id_inraw_1-comments.commentable_type_=_Knovators\LaravelModelCaching\Tests\Fixtures\Post");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturescomment",
        ];

        $result = (new Post)
            ->with("comments")
            ->whereHas("comments")
            ->first()
            ->comments;
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ->first();
        $liveResults = (new UncachedPost)
            ->with("comments")
            ->whereHas("comments")
            ->first()
            ->comments;

        $this->assertEquals($liveResults->first()->description, $result->first()->description);
        $this->assertEquals($liveResults->first()->description, $cachedResults->first()->description);
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }

    public function testLazyloadedRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:comments:knovatorslaravelmodelcachingtestsfixturescomment-comments.commentable_id_=_1-comments.commentable_id_notnull-comments.commentable_type_=_Knovators\LaravelModelCaching\Tests\Fixtures\Post");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturescomment",
        ];

        $result = (new Post)
            ->first()
            ->comments;

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedPost)
            ->first()
            ->comments;

        $this->assertEquals($liveResults->pluck("commentable_id")->values()->toArray(), $result->pluck("commentable_id")->values()->toArray());
        $this->assertEquals($liveResults->pluck("commentable_id")->values()->toArray(), $cachedResults->pluck("commentable_id")->values()->toArray());
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResults);
        $this->assertNotEmpty($liveResults);
    }
}
