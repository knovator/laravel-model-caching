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

class CreateTest extends IntegrationTestCase
{
    

    public function testFirstOrCreateFlushesCacheForModel()
    {
        (new Author)->truncate();
        $noAuthors = (new Author)->get();
        (new Author)->create([
            'name' => 'foo',
            'email' => 'test1@noemail.com',
        ]);
        $authors = (new Author)->get();

        $this->assertEquals(0, $noAuthors->count());
        $this->assertEquals(1, $authors->count());
    }
}
