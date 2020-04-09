<?php namespace Knovators\LaravelModelCaching\Providers;

use Knovators\LaravelModelCaching\Console\Commands\Clear;
use Knovators\LaravelModelCaching\Console\Commands\Publish;
use Knovators\LaravelModelCaching\Helper;
use Illuminate\Support\ServiceProvider;

class Service extends ServiceProvider
{
    protected $defer = false;

    public function boot()
    {
        $configPath = __DIR__ . '/../../config/laravel-model-caching.php';
        $this->mergeConfigFrom($configPath, 'laravel-model-caching');
        $this->commands([
            Clear::class,
            Publish::class,
        ]);
        $this->publishes([
            $configPath => config_path('laravel-model-caching.php'),
        ], "config");
    }

    public function register()
    {
        $this->app->bind("model-cache", Helper::class);
    }
}
