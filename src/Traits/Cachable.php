<?php namespace Knovators\LaravelModelCaching\Traits;

use Knovators\LaravelModelCaching\Traits\LaravelPivotEvents\PivotEventTrait;

/**
 * Trait Cachable
 * @package Knovators\LaravelModelCaching\Traits
 */
trait Cachable
{
    use Caching;
    use ModelCaching;
    use PivotEventTrait {
        ModelCaching::newBelongsToMany insteadof PivotEventTrait;
    }
}
