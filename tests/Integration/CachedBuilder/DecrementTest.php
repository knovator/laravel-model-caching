<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\Profile;
use Knovators\LaravelModelCaching\Tests\Fixtures\Publisher;
use Knovators\LaravelModelCaching\Tests\Fixtures\Store;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedProfile;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPublisher;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedStore;
use Knovators\LaravelModelCaching\Tests\Fixtures\Http\Resources\Author as AuthorResource;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class DecrementTest extends IntegrationTestCase
{
    public function testDecrementingInvalidatesCache()
    {
        $book = (new Book)
            ->find(1);
        $originalPrice = $book->price;
        $originalDescription = $book->description;

        $book->decrement("price", 1.25, ["description" => "test description update"]);
        $book = (new Book)
            ->find(1);

        $this->assertEquals($originalPrice - 1.25, $book->price);
        $this->assertNotEquals($originalDescription, $book->description);
        $this->assertEquals($book->description, "test description update");
    }
}
