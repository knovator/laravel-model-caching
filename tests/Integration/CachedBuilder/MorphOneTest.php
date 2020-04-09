<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Faker\Generator as Faker;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class MorphOneTest extends IntegrationTestCase
{
    public function setUp() : void
    {
        parent::setUp();

        (new Book)
            ->get()
            ->each(function ($book) {
                $book->image()->create([
                    "path" => app(Faker::class)->imageUrl(),
                ]);
            });
        $this->cache()->flush();
    }

    public function testMorphTo()
    {
        $key1 = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-author_id_=_1-testing:{$this->testingSqlitePath}testing.sqlite:image");
        $key2 = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-author_id_=_4-testing:{$this->testingSqlitePath}testing.sqlite:image");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesimage",
        ];

        $books1 = (new Book)
            ->with("image")
            ->where("author_id", 1)
            ->get();
        $cachedResults1 = $this->cache()
            ->tags($tags)
            ->get($key1)['value'];
        $books2 = (new Book)
            ->with("image")
            ->where("author_id", 4)
            ->get();
        $cachedResults2 = $this->cache()
            ->tags($tags)
            ->get($key2)['value'];

        $this->assertEquals($cachedResults1->pluck("images.id"), $books1->pluck("images.id"));
        $this->assertEquals($cachedResults2->pluck("images.id"), $books2->pluck("images.id"));
        $this->assertNotEquals($cachedResults1->pluck("images.id"), $cachedResults2->pluck("images.id"));
        $this->assertNotEquals($books1->pluck("images.id"), $books2->pluck("images.id"));
        $this->assertNotNull($books1->first()->image);
        $this->assertNotNull($books2->first()->image);
        $this->assertNotNull($cachedResults1->first()->image);
        $this->assertNotNull($cachedResults2->first()->image);
    }
}
