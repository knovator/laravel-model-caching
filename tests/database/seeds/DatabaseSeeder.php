<?php

use Knovators\LaravelModelCaching\Tests\Fixtures\Author;
use Knovators\LaravelModelCaching\Tests\Fixtures\Book;
use Knovators\LaravelModelCaching\Tests\Fixtures\Comment;
use Knovators\LaravelModelCaching\Tests\Fixtures\History;
use Knovators\LaravelModelCaching\Tests\Fixtures\Image;
use Knovators\LaravelModelCaching\Tests\Fixtures\Observers\AuthorObserver;
use Knovators\LaravelModelCaching\Tests\Fixtures\Post;
use Knovators\LaravelModelCaching\Tests\Fixtures\Printer;
use Knovators\LaravelModelCaching\Tests\Fixtures\Profile;
use Knovators\LaravelModelCaching\Tests\Fixtures\Publisher;
use Knovators\LaravelModelCaching\Tests\Fixtures\Store;
use Knovators\LaravelModelCaching\Tests\Fixtures\Tag;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedPost;
use Knovators\LaravelModelCaching\Tests\Fixtures\UncachedUser;
use Knovators\LaravelModelCaching\Tests\Fixtures\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        factory(History::class)->create();
        $user = factory(User::class)->create();
        $image = factory(Image::class)->create([
            "imagable_id" => $user->id,
            "imagable_type" => User::class,
        ]);
        factory(Image::class)->create([
            "imagable_id" => $user->id,
            "imagable_type" => UncachedUser::class,
            "path" => $image->path,
        ]);
        factory(Tag::class, 5)->create();
        $post = factory(Post::class)->create();
        $uncachedPost = (new UncachedPost)->first();
        $post->tags()->attach(1);
        $uncachedPost->tags()->attach(1);
        factory(Comment::class, 5)
            ->create([
                "commentable_id" => $post->id,
                "commentable_type" => Post::class,
            ])
            ->each(function ($comment) {
                (new Comment)->create([
                    "commentable_id" => $comment->commentable_id,
                    "commentable_type" => UncachedPost::class,
                    "description" => $comment->description,
                    "subject" => $comment->subject,
                ]);
            });
        $publishers = factory(Publisher::class, 10)->create();
        (new Author)->observe(AuthorObserver::class);
        factory(Author::class, 10)->create()
            ->each(function ($author) use ($publishers) {
                $profile = factory(Profile::class)
                    ->make();
                $profile->author_id = $author->id;
                $profile->save();
                factory(Book::class, random_int(5, 25))
                    ->create([
                        "author_id" => $author->id,
                        "publisher_id" => $publishers[rand(0, 9)]->id,
                    ])
                    ->each(function ($book) use ($author, $publishers) {
                        factory(Printer::class)->create([
                            "book_id" => $book->id,
                        ]);
                    });
                factory(Profile::class)->make([
                    'author_id' => $author->id,
                ]);
            });

        $bookIds = (new Book)->all()->pluck('id');
        factory(Store::class, 10)->create()
            ->each(function ($store) use ($bookIds) {
                $store->books()->sync(rand($bookIds->min(), $bookIds->max()));
            });
    }
}
