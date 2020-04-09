<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WhereJsonContainsTest extends IntegrationTestCase
{
    use RefreshDatabase;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'pgsql');
        $app['config']->set('database.connections.pgsql.host', env("PGSQL_HOST", "127.0.0.1"));
        $app['config']->set('database.connections.pgsql.database', "testing");
        $app['config']->set('database.connections.pgsql.username', "homestead");
        $app['config']->set('database.connections.pgsql.password', "secret");
    }

    public function setUp() : void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    public function testWithInUsingCollectionQuery()
    {
        $key = sha1("knovators:laravel-model-caching:pgsql:testing:authors:knovatorslaravelmodelcachingtestsfixturesauthor-finances->total_jsoncontains_5000-authors.deleted_at_null");
        $tags = [
            'knovators:laravel-model-caching:pgsql:testing:knovatorslaravelmodelcachingtestsfixturesauthor',
        ];

        $authors = (new Author)
            ->whereJsonContains("finances->total", 5000)
            ->get();
        $liveResults = (new UncachedAuthor)
            ->whereJsonContains("finances->total", 5000)
            ->get();

        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];

        $this->assertEquals($liveResults->pluck("id"), $authors->pluck("id"));
        $this->assertEquals($liveResults->pluck("id"), $cachedResults->pluck("id"));
    }
}
