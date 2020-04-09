<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class DeleteTest extends IntegrationTestCase
{
    public function testDecrementingInvalidatesCache()
    {
        $book = (new Book)
            ->orderBy("id", "DESC")
            ->first();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook_orderBy_id_desc-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
        ];

        $beforeDeleteCachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $book->delete();
        $afterDeleteCachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;

        $this->assertEquals($beforeDeleteCachedResults->id, $book->id);
        $this->assertNotEquals($beforeDeleteCachedResults, $afterDeleteCachedResults);
        $this->assertNull($afterDeleteCachedResults);
    }
}
