<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class InRandomOrderQueryTest extends IntegrationTestCase
{
    public function testInRandomOrderCachesResults()
    {
        $cachedBook1 = (new Book)
            ->inRandomOrder()
            ->first();
        $cachedBook2 = (new Book)
            ->inRandomOrder()
            ->first();
        $book1 = (new UncachedBook)
            ->inRandomOrder()
            ->first();
        $book2 = (new UncachedBook)
            ->inRandomOrder()
            ->first();

        $this->assertNotEquals($book1, $book2);
        $this->assertNotEquals($cachedBook1, $cachedBook2);
    }
}
