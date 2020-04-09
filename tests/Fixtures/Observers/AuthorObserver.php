<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures\Observers;

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;

class AuthorObserver
{
    public function saving(Author $author)
    {
        $author->email .= "";
    }

    public function retrieved(Author $author)
    {
        $author->email .= "";
    }
}
