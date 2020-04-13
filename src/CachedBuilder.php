<?php namespace Knovators\LaravelModelCaching;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
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
//    function __construct(
//        ConnectionInterface $connection,
//        Grammar $grammar = null,
//        Processor $processor = null,
//        $useCollections = null
//    ) {
//        parent::__construct($connection, $grammar, $processor, $useCollections);
//    }
    use Buildable;
    use BuilderCaching;
    use Caching;
}
