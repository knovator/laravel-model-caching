<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\Comment;
use Knovators\LaravelModelCaching\Tests\Fixtures\Publisher;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class WithCountTest extends IntegrationTestCase
{
    public function testWithCountUpdatesAfterRecordIsAdded()
    {
        $author1 = (new Author)
            ->withCount("books")
            ->first();
        factory(Book::class, 1)
            ->make()
            ->each(function ($book) use ($author1) {
                $publisher = (new Publisher)->first();
                $book->author()->associate($author1);
                $book->publisher()->associate($publisher);
                $book->save();
            });

        $author2 = (new Author)
            ->withCount("books")
            ->where("id", $author1->id)
            ->first();

        $this->assertNotEquals($author1->books_count, $author2->books_count);
        $this->assertEquals($author1->books_count + 1, $author2->books_count);
    }

    public function testWithCountOnMorphManyRelationshipUpdatesAfterRecordIsAdded()
    {
        $book1 = (new Book)
            ->withCount("comments")
            ->first();
        $comment = factory(Comment::class, 1)
            ->create()
            ->first();

        $book1->comments()->save($comment);

        $book2 = (new Book)
            ->withCount("comments")
            ->where("id", $book1->id)
            ->first();

        $this->assertNotEquals($book1->comments_count, $book2->comments_count);
        $this->assertEquals($book1->comments_count + 1, $book2->comments_count);
    }
}
