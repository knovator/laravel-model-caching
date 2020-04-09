<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Knovators\LaravelModelCaching\Tests\Fixtures\AuthorBeginsWithA;
use Knovators\LaravelModelCaching\Tests\Fixtures\AuthorWithInlineGlobalScope;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthorWithInlineGlobalScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScopeTest extends IntegrationTestCase
{
    public function testScopeClauseParsing()
    {
        $author = factory(Author::class, 1)
            ->create(['name' => 'Anton'])
            ->first();
        $authors = (new Author)
            ->startsWithA()
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-name_like_A%-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->startsWithA()
            ->get();

        $this->assertTrue($authors->contains($author));
        $this->assertTrue($cachedResults->contains($author));
        $this->assertTrue($liveResults->contains($author));
    }

    public function testScopeClauseWithParameter()
    {
        $author = factory(Author::class, 1)
            ->create(['name' => 'Boris'])
            ->first();
        $authors = (new Author)
            ->nameStartsWith("B")
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-name_like_B%-authors.deleted_at_null");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->nameStartsWith("B")
            ->get();

        $this->assertTrue($authors->contains($author));
        $this->assertTrue($cachedResults->contains($author));
        $this->assertTrue($liveResults->contains($author));
    }

    public function testGlobalScopesAreCached()
    {
        $author = factory(UncachedAuthor::class, 1)
            ->create(['name' => 'Alois'])
            ->first();
        $authors = (new AuthorBeginsWithA)
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthorbeginswitha-name_like_A%");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthorbeginswitha"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->nameStartsWith("A")
            ->get();

        $this->assertTrue($authors->contains($author));
        $this->assertTrue($cachedResults->contains($author));
        $this->assertTrue($liveResults->contains($author));
    }

    public function testInlineGlobalScopesAreCached()
    {
        $author = factory(UncachedAuthor::class, 1)
            ->create(['name' => 'Alois'])
            ->first();
        $authors = (new AuthorWithInlineGlobalScope)
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthorwithinlineglobalscope-authors.deleted_at_null-name_like_A%");
        $tags = ["knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthorwithinlineglobalscope"];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthorWithInlineGlobalScope)
            ->get();

        $this->assertTrue($authors->contains($author));
        $this->assertTrue($cachedResults->contains($author));
        $this->assertTrue($liveResults->contains($author));
    }

    public function testLocalScopesInRelationship()
    {
        $first = "A";
        $second = "B";
        $authors1 = (new Author)
            ->with(['books' => static function (HasMany $model) use ($first) {
                $model->startsWith($first);
            }])
            ->get();
        $authors2 = (new Author)
            ->disableModelCaching()
            ->with(['books' => static function (HasMany $model) use ($second) {
                $model->startsWith($second);
            }])
            ->get();

        // $this->assertNotEquals($authors1, $authors2);
        $this->markTestSkipped();
    }
}
