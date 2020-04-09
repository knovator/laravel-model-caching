<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class SelectTest extends IntegrationTestCase
{
    public function testSelectWithRawColumns()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:books:knovatorslaravelmodelcachingtestsfixturesbook_author_id_AVG(id) AS averageIds_orderBy_author_id_asc");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesbook",
        ];
        $selectArray = [
            app("db")->raw("author_id"),
            app("db")->raw("AVG(id) AS averageIds"),
        ];

        $books = (new Book)
            ->select($selectArray)
            ->groupBy("author_id")
            ->orderBy("author_id")
            ->get()
            ->toArray();
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ->toArray();
        $liveResults = (new Book)
            ->select($selectArray)
            ->groupBy("author_id")
            ->orderBy("author_id")
            ->get()
            ->toArray();

        $this->assertEquals($liveResults, $books);
        $this->assertEquals($liveResults, $cachedResults);
    }

    public function testSelectFieldsAreCached()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_id_name-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $authorFields = (new Author)
            ->select("id", "name")
            ->first()
            ->getAttributes();
        $uncachedFields = (new UncachedAuthor)
            ->select("id", "name")
            ->first()
            ->getAttributes();
        $cachedFields = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ->getAttributes();

        $this->assertEquals($cachedFields, $authorFields);
        $this->assertEquals($cachedFields, $uncachedFields);
    }

    public function testAddSelectMethodOnModel()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_(SELECT id FROM authors WHERE id = 1)-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $result = (new Author)
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->first();
        $uncachedResult = (new UncachedAuthor)
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->first();
        $uncachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];

        $this->assertEquals($uncachedResult, $result);
        $this->assertEquals($uncachedResult, $uncachedResult);
    }

    public function testAddSelectMethodOnBuilder()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_(SELECT id FROM authors WHERE id = 1)_(SELECT id FROM authors WHERE id = 1)-id_=_1-authors.deleted_at_null-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $result = (new Author)
            ->where("id", 1)
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->first();
        $uncachedResult = (new UncachedAuthor)
            ->where("id", 1)
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->addSelect(app("db")->raw("(SELECT id FROM authors WHERE id = 1)"))
            ->first();
        $uncachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];

        $this->assertEquals($uncachedResult, $result);
        $this->assertEquals($uncachedResult, $uncachedResult);
    }
}
