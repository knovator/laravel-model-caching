<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class FirstTest extends IntegrationTestCase
{
    public function testFirstReturnsAllAttributesForModel()
    {
        $author = (new Author)
            ->where("id", "=", 1)
            ->first();
        $uncachedAuthor = (new UncachedAuthor)
            ->where("id", "=", 1)
            ->first();

        $this->assertEquals($author->id, $uncachedAuthor->id);
        $this->assertEquals($author->created_at, $uncachedAuthor->created_at);
        $this->assertEquals($author->updated_at, $uncachedAuthor->updated_at);
        $this->assertEquals($author->email, $uncachedAuthor->email);
        $this->assertEquals($author->name, $uncachedAuthor->name);
    }

    public function testFirstIsNotTheSameAsAll()
    {
        $authors = (new Author)
            ->all();
        $author = (new Author)
            ->first();

        $this->assertNotEquals($authors, $author);
    }
}
