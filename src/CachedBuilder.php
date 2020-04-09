<?php namespace Knovators\LaravelModelCaching;

use Knovators\LaravelModelCaching\Traits\BuilderCaching;
use Knovators\LaravelModelCaching\Traits\Buildable;
use Knovators\LaravelModelCaching\Traits\Caching;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Class CachedBuilder
 * @package Knovators\LaravelModelCaching
 */
class CachedBuilder extends EloquentBuilder
{
    use Buildable;
    use BuilderCaching;
    use Caching;
}
