<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\Profile;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedProfile;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class LazyLoadTest extends IntegrationTestCase
{
    public function testBelongsToRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.id_=_1-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $result = (new Book)
            ->where("id", 1)
            ->first()
            ->author;
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $uncachedResult = (new UncachedBook)
            ->where("id", 1)
            ->first()
            ->author;

        $this->assertEquals($uncachedResult->id, $result->id);
        $this->assertEquals($uncachedResult->id, $cachedResult->id);
        $this->assertEquals(Author::class, get_class($result));
        $this->assertEquals(Author::class, get_class($cachedResult));
        $this->assertEquals(UncachedAuthor::class, get_class($uncachedResult));
        $this->assertNotNull($result);
        $this->assertNotNull($cachedResult);
        $this->assertNotNull($uncachedResult);
    }

    public function testHasManyRelationship()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook-books.author_id_=_1-books.author_id_notnull");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
        ];

        $result = (new Author)
            ->find(1)
            ->books;
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $uncachedResult = (new UncachedAuthor)
            ->find(1)
            ->books;

        $this->assertEquals($uncachedResult->pluck("id"), $result->pluck("id"));
        $this->assertEquals($uncachedResult->pluck("id"), $cachedResult->pluck("id"));
        $this->assertEquals(Book::class, get_class($result->first()));
        $this->assertEquals(Book::class, get_class($cachedResult->first()));
        $this->assertEquals(UncachedBook::class, get_class($uncachedResult->first()));
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResult);
        $this->assertNotEmpty($uncachedResult);
    }

    public function testHasOneRelationship()
    {
        $authorId = (new UncachedProfile)
            ->first()
            ->author_id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:profiles:knovatorslaravelmodelcachingtestsfixturesprofile-profiles.author_id_=_{$authorId}-profiles.author_id_notnull-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesprofile",
        ];

        $result = (new Author)
            ->find($authorId)
            ->profile;
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $uncachedResult = (new UncachedAuthor)
            ->find($authorId)
            ->profile;

        $this->assertEquals($uncachedResult->id, $result->id);
        $this->assertEquals($uncachedResult->id, $cachedResult->id);
        $this->assertEquals(Profile::class, get_class($result->first()));
        $this->assertEquals(Profile::class, get_class($cachedResult->first()));
        $this->assertEquals(UncachedProfile::class, get_class($uncachedResult->first()));
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($cachedResult);
        $this->assertNotEmpty($uncachedResult);
    }
}
