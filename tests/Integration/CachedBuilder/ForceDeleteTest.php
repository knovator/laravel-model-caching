<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class ForceDeleteTest extends IntegrationTestCase
{
    public function testForceDeleteClearsCache()
    {
        $author = (new Author)
            ->where("id", 1)
            ->get();

        $resultsBefore = $this
            ->cache()
            ->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            ])
            ->get(sha1(
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-id_=_1-authors.deleted_at_null"
            ))["value"];

        (new Author)
            ->where("id", 1)
            ->forceDelete();
        $resultsAfter = $this
            ->cache()
            ->tags([
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
            ])
            ->get(sha1(
                "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-id_=_1"
            ))["value"]
            ?? null;

        $this->assertEquals(get_class($resultsBefore), get_class($author));
        $this->assertNotNull($resultsBefore);
        $this->assertNull($resultsAfter);
    }
}
