<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\BookWithUncachedStore;
use Knovators\LaravelModelCaching\Tests\Fixtures\Store;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedBook;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;

class BelongsToManyTest extends IntegrationTestCase
{
    public function testLazyLoadingRelationship()
    {
        $bookId = (new Store)
            ->disableModelCaching()
            ->with("books")
            ->first()
            ->books
            ->first()
            ->id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];

        $stores = (new Book)
            ->find($bookId)
            ->stores;
        $cachedStores = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $uncachedBook = (new UncachedBook)
            ->find($bookId);
        $uncachedStores = $uncachedBook->stores;

        $this->assertEquals($uncachedStores->pluck("id"), $stores->pluck("id"));
        $this->assertEquals($uncachedStores->pluck("id"), $cachedStores->pluck("id"));
        $this->assertNotNull($cachedStores);
        $this->assertNotNull($uncachedStores);
    }

    public function testInvalidatingCacheWhenAttaching()
    {
        $bookId = (new Store)
            ->disableModelCaching()
            ->with("books")
            ->first()
            ->books
            ->first()
            ->id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:stores:knovatorslaravelmodelcachingtestsfixturesstore-testing:{$this->testingSqlitePath}testing.sqlite:books-first");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];
        $newStore = factory(Store::class)
            ->create();
        $result = (new Book)
            ->find($bookId)
            ->stores;

        (new Book)
            ->find($bookId)
            ->stores()
            ->attach($newStore->id);
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;

        $this->assertNotEmpty($result);
        $this->assertNull($cachedResult);
    }

    public function testInvalidatingCacheWhenDetaching()
    {
        $bookId = (new Store)
            ->disableModelCaching()
            ->with("books")
            ->first()
            ->books
            ->first()
            ->id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];
        $result = (new Book)
            ->find($bookId)
            ->stores;

        (new Book)
            ->find($bookId)
            ->stores()
            ->detach($result->first()->id);
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;

        $this->assertNotEmpty($result);
        $this->assertNull($cachedResult);
    }

    public function testInvalidatingCacheWhenUpdating()
    {
        $bookId = (new Store)
            ->disableModelCaching()
            ->with("books")
            ->first()
            ->books
            ->first()
            ->id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
        ];
        $result = (new Book)
            ->find($bookId)
            ->stores;

        $store = $result->first();
        $store->address = "test address";
        $store->save();
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;

        $this->assertNotEmpty($result);
        $this->assertNull($cachedResult);
    }

    public function testUncachedRelatedModelDoesntCache()
    {
        $bookId = (new Store)
            ->disableModelCaching()
            ->with("books")
            ->first()
            ->books
            ->first()
            ->id;
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesuncachedstore",
        ];

        $result = (new BookWithUncachedStore)
            ->find($bookId)
            ->uncachedStores;
        $cachedResult = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value']
            ?? null;
        $uncachedResult = (new UncachedBook)
            ->find($bookId)
            ->stores;

        $this->assertEquals($uncachedResult->pluck("id"), $result->pluck("id"));
        $this->assertNull($cachedResult);
        $this->assertNotNull($result);
        $this->assertNotNull($uncachedResult);
    }

    // /** @group test */
    // public function testUncachedDetachesFromCached()
    // {
    //     // $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
    //     // $tags = [
    //     //     "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
    //     // ];

    //     $store = (new StoreWithUncachedBooks)
    //         ->with("books")
    //         ->has("books")
    //         ->first();
    //     $store->books()
    //         ->detach();
    //     // $store->delete();
    //     // dd($results);
    //     // $cachedResult = $this
    //     //     ->cache()
    //     //     ->tags($tags)
    //     //     ->get($key)['value'];

    //     // $this->assertNotEmpty($result);
    //     // $this->assertNull($cachedResult);
    // }

    // /** @group test */
    // public function testCachedDetachesFromUncached()
    // {
    //     // $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
    //     // $tags = [
    //     //     "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
    //     // ];
    //     $book = (new UncachedBookWithStores)
    //         ->with("stores")
    //         ->has("stores")
    //         ->first();
    //     $book->stores()
    //         ->detach();
    //     // $book->delete();
    //     // dd($results);
    //     // $cachedResult = $this
    //     //     ->cache()
    //     //     ->tags($tags)
    //     //     ->get($key)['value'];

    //     // $this->assertNotEmpty($result);
    //     // $this->assertNull($cachedResult);
    // }

    // public function testDetachingFiresEvent()
    // {
    //     // $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:book-store:knovatorslaravelmodelcachingcachedbelongstomany-book_store.book_id_=_{$bookId}");
    //     // $tags = [
    //     //     "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesstore",
    //     // ];

    //     $store = (new Store)
    //         ->with("books")
    //         ->has("books")
    //         ->first();
    //     $store->books()
    //         ->detach();
    //     $store->delete();
    //     // dd($results);
    //     // $cachedResult = $this
    //     //     ->cache()
    //     //     ->tags($tags)
    //     //     ->get($key)['value'];

    //     // $this->assertNotEmpty($result);
    //     // $this->assertNull($cachedResult);
    // }
}
