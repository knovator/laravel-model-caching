<?php namespace Knovators\LaravelModelCaching\Tests\Feature;

use Knovators\LaravelModelCaching\Tests\FeatureTestCase;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;

class PaginationTest extends FeatureTestCase
{
    public function testPaginationProvidesDifferentLinksOnDifferentPages()
    {
        // Checking the version start with 5.6, 5.7, 5.8 or 6.
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
        }

        // Checking the version 5.4 and 5.5
        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
        }

        $book = (new Book)
            ->take(11)
            ->get()
            ->last();
        $page1 = $this->visit("pagination-test");

        $page1->see($page1ActiveLink);
        $page2 = $page1->click("2");
        $page2->see($page2ActiveLink);
        $page2->see($book->title);
    }

    public function testAdvancedPagination()
    {
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
        }

        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
        }

        $response = $this->visit("pagination-test?page[size]=1");

        $response->see($page1ActiveLink);
    }

    public function testCustomPagination()
    {
        if (preg_match("/^((5\.[6-8])|(6\.)|(7\.))/", app()->version())) {
            $page1ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
            $page2ActiveLink = '<li class="page-item active" aria-current="page"><span class="page-link">2</span></li>';
        }

        if (preg_match("/^5\.[4-5]/", app()->version())) {
            $page1ActiveLink = '<li class="active"><span>1</span></li>';
            $page2ActiveLink = '<li class="active"><span>2</span></li>';
        }

        $response = $this->visit("pagination-test2?custom-page=2");

        $response->see($page2ActiveLink);
    }
}
