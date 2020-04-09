<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class WhereTest extends IntegrationTestCase
{
    public function testWithQuery()
    {
        $books = (new Book)
            ->where(function ($query) {
                $query->where("id", ">", "1")
                    ->where("id", "<", "5");
            })
            ->get();
        $uncachedBooks = (new UncachedBook)
            ->where(function ($query) {
                $query->where("id", ">", "1")
                    ->where("id", "<", "5");
            })
            ->get();

        $this->assertEquals($books->pluck("id"), $uncachedBooks->pluck("id"));
    }

    public function testColumnsRelationshipWhereClauseParsing()
    {
        $author = (new Author)
            ->orderBy('name')
            ->first();
        $authors = (new Author)
            ->where('name', '=', $author->name)
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-name_=_{$author->name}-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->where('name', '=', $author->name)
            ->get();

        $this->assertEmpty($authors->diffKeys($cachedResults));
        $this->assertEmpty($liveResults->diffKeys($cachedResults));
    }

    private function processWhereClauseTestWithOperator(string $operator)
    {
        $author = (new Author)->first();
        $authors = (new Author)
            ->where('name', $operator, $author->name)
            ->get();
        $keyParts = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-name",
            '_',
            str_replace(' ', '_', strtolower($operator)),
            '_',
            $author->name,
            "-authors.deleted_at_null"
        ];
        $key = sha1(implode('', $keyParts));
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->where('name', $operator, $author->name)
            ->get();

        $this->assertEmpty($authors->diffKeys($cachedResults));
        $this->assertEmpty($liveResults->diffKeys($cachedResults));
    }

    public function testWhereClauseParsingOfOperators()
    {
        $this->processWhereClauseTestWithOperator('=');
        $this->processWhereClauseTestWithOperator('!=');
        $this->processWhereClauseTestWithOperator('<>');
        $this->processWhereClauseTestWithOperator('>');
        $this->processWhereClauseTestWithOperator('<');
        $this->processWhereClauseTestWithOperator('LIKE');
        $this->processWhereClauseTestWithOperator('NOT LIKE');
    }

    public function testTwoWhereClausesAfterEachOther()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-id_>_0-id_<_100-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $authors = (new Author)
            ->where("id", ">", 0)
            ->where("id", "<", 100)
            ->get();
        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->where("id", ">", 0)
            ->where("id", "<", 100)
            ->get();

        $this->assertEmpty($authors->diffKeys($cachedResults));
        $this->assertEmpty($liveResults->diffKeys($cachedResults));
    }

    public function testWhereUsesCorrectBinding()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-nested-name_like_B%-name_like_G%-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $authors = (new Author)
            ->where("name", "LIKE", "A%")
            ->orWhere("name", "LIKE", "D%")
            ->get();
        $authors = (new Author)
            ->where("name", "LIKE", "B%")
            ->orWhere("name", "LIKE", "G%")
            ->get();
        $cachedResults = collect($this->cache()
            ->tags($tags)
            ->get($key)['value']);
        $liveResults = (new UncachedAuthor)
            ->where("name", "LIKE", "B%")
            ->orWhere("name", "LIKE", "G%")
            ->get();

        $this->assertEquals($liveResults->toArray(), $authors->toArray());
        $this->assertEquals($liveResults->toArray(), $cachedResults->toArray());
    }
}
