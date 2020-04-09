<?php namespace Knovators\LaravelModelCaching;

use Knovators\LaravelPivotEvents\Traits\FiresPivotEventsTrait;
use Knovators\LaravelModelCaching\Traits\Buildable;
use Knovators\LaravelModelCaching\Traits\BuilderCaching;
use Knovators\LaravelModelCaching\Traits\Caching;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class CachedBelongsToMany
 * @package Knovators\LaravelModelCaching
 */
class CachedBelongsToMany extends BelongsToMany
{
    use Buildable;
    use BuilderCaching;
    use Caching;
    use FiresPivotEventsTrait;
}
