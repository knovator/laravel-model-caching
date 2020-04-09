<?php namespace Knovators\LaravelModelCaching\Traits;

use Illuminate\Container\Container;

/**
 * Trait CachePrefixing
 * @package Knovators\LaravelModelCaching\Traits
 */
trait CachePrefixing
{

    /**
     * @return string
     */
    protected function getCachePrefix() : string {
        $cachePrefix = "knovators:laravel-model-caching:";
        $useDatabaseKeying = Container::getInstance()
                                      ->make("config")
                                      ->get("laravel-model-caching.use-database-keying");

        if ($useDatabaseKeying) {
            $cachePrefix .= $this->getConnectionName() . ":";
            $cachePrefix .= $this->getDatabaseName() . ":";
        }

        $cachePrefix .= Container::getInstance()
                                 ->make("config")
                                 ->get("laravel-model-caching.cache-prefix", "");

        if ($this->model
            && property_exists($this->model, "cachePrefix")
        ) {
            $cachePrefix .= $this->model->cachePrefix . ":";
        }

        return $cachePrefix;
    }

    /**
     * @return string
     */
    protected function getConnectionName() : string {
        return $this->model->getConnection()->getName();
    }

    /**
     * @return string
     */
    protected function getDatabaseName() : string {
        return $this->model->getConnection()->getDatabaseName();
    }
}
