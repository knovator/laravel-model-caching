<?php namespace Knovators\LaravelModelCaching;

use Illuminate\Container\Container;

class Helper
{

    /**
     * @param callable $closure
     * @return mixed
     */
    public function runDisabled(callable $closure)
    {
        $originalSetting = Container::getInstance()
            ->make("config")
            ->get('laravel-model-caching.enabled');

        Container::getInstance()
            ->make("config")
            ->set(['laravel-model-caching.enabled' => false]);

        $result = $closure();

        Container::getInstance()
            ->make("config")
            ->set(['laravel-model-caching.enabled' => $originalSetting]);

        return $result;
    }
}
