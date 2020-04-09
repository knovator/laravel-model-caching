<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class UpdateExistingPivotTest extends IntegrationTestCase
{
    public function testInRandomOrderCachesResults()
    {
        $book = (new Book)
            ->with("stores")
            ->whereHas("stores")
            ->first();
        $book->stores()
            ->updateExistingPivot(
                $book->stores->first()->id,
                ["test" => "value"]
            );
        $updatedCount = (new Book)
            ->with("stores")
            ->whereHas("stores")
            ->first()
            ->stores()
            ->wherePivot("test", "value")
            ->count();

        $this->assertEquals(1, $updatedCount);
    }
}
