<?php namespace Knovators\LaravelModelCaching\Tests\Integration;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

/**
* @SuppressWarnings(PHPMD.TooManyPublicMethods)
* @SuppressWarnings(PHPMD.TooManyMethods)
 */
class DisabledCachedBuilderTest extends IntegrationTestCase
{
    public function testAvgModelResultsIsNotCached()
    {
        $authorId = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->avg('id');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-avg_id");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResult = (new UncachedAuthor)
            ->with('books', 'profile')
            ->avg('id');

        $this->assertEquals($authorId, $liveResult);
        $this->assertNull($cachedResult);
    }

    public function testChunkModelResultsIsNotCached()
    {
        $cachedChunks = collect([
            'authors' => collect(),
            'keys' => collect(),
        ]);
        $chunkSize = 3;
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];
        $uncachedChunks = collect();

        $authors = (new Author)->with('books', 'profile')
            ->disableCache()
            ->chunk($chunkSize, function ($chunk) use (&$cachedChunks, $chunkSize) {
                $offset = '';

                if ($cachedChunks['authors']->count()) {
                    $offsetIncrement = $cachedChunks['authors']->count() * $chunkSize;
                    $offset = "-offset_{$offsetIncrement}";
                }

                $cachedChunks['authors']->push($chunk);
                $cachedChunks['keys']->push(sha1(
                    "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile_orderBy_authors.id_asc{$offset}-limit_3"
                ));
            });

        $liveResults = (new UncachedAuthor)->with('books', 'profile')
            ->chunk($chunkSize, function ($chunk) use (&$uncachedChunks) {
                $uncachedChunks->push($chunk);
            });

        for ($index = 0; $index < $cachedChunks['authors']->count(); $index++) {
            $key = $cachedChunks['keys'][$index];
            $cachedResults = $this->cache()
                ->tags($tags)
                ->get($key);

            $this->assertNull($cachedResults);
            $this->assertEquals($authors, $liveResults);
        }
    }

    public function testCountModelResultsIsNotCached()
    {
        $authors = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->count();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-count");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResults = (new UncachedAuthor)
            ->with('books', 'profile')
            ->count();

        $this->assertEquals($authors, $liveResults);
        $this->assertNull($cachedResults);
    }

    public function testCursorModelResultsIsNotCached()
    {
        $authors = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->cursor();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-cursor");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResults = collect(
            (new UncachedAuthor)
                ->with('books', 'profile')
                ->cursor()
        );

        $this->assertEmpty($liveResults->diffKeys($authors));
        $this->assertNull($cachedResults);
    }

    public function testFindModelResultsIsNotCached()
    {
        $author = (new Author)
            ->with('books')
            ->disableCache()
            ->find(1);
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_1");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResult = (new UncachedAuthor)
            ->find(1);

        $this->assertEquals($liveResult->name, $author->name);
        $this->assertNull($cachedResult);
    }

    public function testGetModelResultsIsNotCached()
    {
        $authors = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->get();
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResults = (new UncachedAuthor)
            ->with('books', 'profile')
            ->get();

        $this->assertEmpty($liveResults->diffKeys($authors));
        $this->assertNull($cachedResults);
    }

    public function testMaxModelResultsIsNotCached()
    {
        $authorId = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->max('id');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-max_id");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResult = (new UncachedAuthor)
            ->with('books', 'profile')
            ->max('id');

        $this->assertEquals($authorId, $liveResult);
        $this->assertNull($cachedResult);
    }

    public function testMinModelResultsIsNotCached()
    {
        $authorId = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->min('id');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-min_id");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResult = (new UncachedAuthor)
            ->with('books', 'profile')
            ->min('id');

        $this->assertEquals($authorId, $liveResult);
        $this->assertNull($cachedResult);
    }

    public function testPluckModelResultsIsNotCached()
    {
        $authors = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->pluck('name', 'id');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_name-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-pluck_name_id");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResults = (new UncachedAuthor)
            ->with('books', 'profile')
            ->pluck('name', 'id');

        $this->assertEmpty($liveResults->diffKeys($authors));
        $this->assertNull($cachedResults);
    }

    public function testSumModelResultsIsNotCached()
    {
        $authorId = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->sum('id');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-sum_id");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);
        $liveResult = (new UncachedAuthor)
            ->with('books', 'profile')
            ->sum('id');

        $this->assertEquals($authorId, $liveResult);
        $this->assertNull($cachedResult);
    }

    public function testValueModelResultsIsNotCached()
    {
        $author = (new Author)
            ->with('books', 'profile')
            ->disableCache()
            ->value('name');
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor_name-testing:{$this->testingSqlitePath}testing.sqlite:books-testing:{$this->testingSqlitePath}testing.sqlite:profile-first");
        $tags = [
            'knovatorslaravelmodelcachingtestsfixturesauthor',
            'knovatorslaravelmodelcachingtestsfixturesbook',
            'knovatorslaravelmodelcachingtestsfixturesprofile',
        ];

        $cachedResult = $this->cache()
            ->tags($tags)
            ->get($key);

        $liveResult = (new UncachedAuthor)
            ->with('books', 'profile')
            ->value('name');

        $this->assertEquals($author, $liveResult);
        $this->assertNull($cachedResult);
    }

    public function testPaginationIsCached()
    {
        $authors = (new Author)
            ->disableCache()
            ->paginate(3);

        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-paginate_by_3_page_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = $this->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;
        $liveResults = (new UncachedAuthor)
            ->paginate(3);

        $this->assertNull($cachedResults);
        $this->assertEquals($liveResults->toArray(), $authors->toArray());
    }
}
