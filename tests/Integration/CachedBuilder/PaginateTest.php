<?php namespace Knovators\LaravelModelCaching\Tests\Integration\CachedBuilder;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedAuthor;
use Knovators\LaravelModelCaching\Tests\IntegrationTestCase;
use Illuminate\Support\Str;

/**
* @SuppressWarnings(PHPMD.TooManyPublicMethods)
* @SuppressWarnings(PHPMD.TooManyMethods)
 */
class PaginateTest extends IntegrationTestCase
{
    public function testPaginationIsCached()
    {
        $authors = (new Author)
            ->paginate(3);

        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-paginate_by_3_page_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->paginate(3);

        $this->assertEquals($cachedResults, $authors);
        $this->assertEquals($liveResults->pluck("email"), $authors->pluck("email"));
        $this->assertEquals($liveResults->pluck("name"), $authors->pluck("name"));
    }

    public function testPaginationReturnsCorrectLinks()
    {
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
            $page24ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">24</span></li>';
        }

        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
            $page24ActiveLink = '<li class="active"><span>24</span></li>';
        }

        $booksPage1 = (new Book)
            ->paginate(2);
        $booksPage2 = (new Book)
            ->paginate(2, ['*'], null, 2);
        $booksPage24 = (new Book)
            ->paginate(2, ['*'], null, 24);

        $this->assertCount(2, $booksPage1);
        $this->assertCount(2, $booksPage2);
        $this->assertCount(2, $booksPage24);
        $this->assertStringContainsString($page1ActiveLink, (string) $booksPage1->links());
        $this->assertStringContainsString($page2ActiveLink, (string) $booksPage2->links());
        $this->assertStringContainsString($page24ActiveLink, (string) $booksPage24->links());
    }

    public function testPaginationWithOptionsReturnsCorrectLinks()
    {
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
            $page24ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">24</span></li>';
        }

        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
            $page24ActiveLink = '<li class="active"><span>24</span></li>';
        }

        $booksPage1 = (new Book)
            ->paginate(2);
        $booksPage2 = (new Book)
            ->paginate(2, ['*'], null, 2);
        $booksPage24 = (new Book)
            ->paginate(2, ['*'], null, 24);

        $this->assertCount(2, $booksPage1);
        $this->assertCount(2, $booksPage2);
        $this->assertCount(2, $booksPage24);
        $this->assertStringContainsString($page1ActiveLink, (string) $booksPage1->links());
        $this->assertStringContainsString($page2ActiveLink, (string) $booksPage2->links());
        $this->assertStringContainsString($page24ActiveLink, (string) $booksPage24->links());
    }

    public function testPaginationWithCustomOptionsReturnsCorrectLinks()
    {
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
            $page24ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">24</span></li>';
        }

        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
            $page24ActiveLink = '<li class="active"><span>24</span></li>';
        }

        $booksPage1 = (new Book)
            ->paginate('2');
        $booksPage2 = (new Book)
            ->paginate('2', ['*'], 'pages', 2);
        $booksPage24 = (new Book)
            ->paginate('2', ['*'], 'pages', 24);

        $this->assertCount(2, $booksPage1);
        $this->assertCount(2, $booksPage2);
        $this->assertCount(2, $booksPage24);
        $this->assertStringContainsString($page1ActiveLink, (string) $booksPage1->links());
        $this->assertStringContainsString($page2ActiveLink, (string) $booksPage2->links());
        $this->assertStringContainsString($page24ActiveLink, (string) $booksPage24->links());
    }

    public function testCustomPageNamePagination()
    {
        $key = sha1("knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:authors:knovatorslaravelmodelcachingtestsfixturesauthor-authors.deleted_at_null-paginate_by_3_custom-page_1");
        $tags = [
            "knovators:laravel-model-caching:testing:{$this->testingSqlitePath}testing.sqlite:knovatorslaravelmodelcachingtestsfixturesauthor",
        ];

        $authors = (new Author)
            ->paginate(3, ["*"], "custom-page");
        $cachedResults = $this
            ->cache()
            ->tags($tags)
            ->get($key)['value'];
        $liveResults = (new UncachedAuthor)
            ->paginate(3, ["*"], "custom-page");

        $this->assertEquals($cachedResults, $authors);
        $this->assertEquals($liveResults->pluck("email"), $authors->pluck("email"));
        $this->assertEquals($liveResults->pluck("name"), $authors->pluck("name"));
    }

    public function testCustomPageNamePaginationFetchesCorrectPages()
    {
        $authors1 = (new Author)
            ->paginate(3, ["*"], "custom-page", 1);
        $authors2 = (new Author)
            ->paginate(3, ["*"], "custom-page", 2);

        $this->assertNotEquals($authors1->pluck("id"), $authors2->pluck("id"));
    }
}
