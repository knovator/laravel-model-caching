<?php namespace Knovators\LaravelModelCaching\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

/**
 * Class Clear
 * @package Knovators\LaravelModelCaching\Console\Commands
 */
class Clear extends Command
{

    protected $signature = 'modelCache:clear {--model=}';
    protected $description = 'Flush cache for a given model. If no model is given, entire model-cache is flushed.';

    /**
     * @return int
     */
    public function handle() {
        $option = $this->option('model');
        if (!$option) {
            return $this->flushEntireCache();
        }
        return $this->flushModelCache($option);
    }

    /**
     * @return int
     */
    protected function flushEntireCache() : int {
        $config = Container::getInstance()
                           ->make("config")
                           ->get('laravel-model-caching.store');

        Container::getInstance()
                 ->make("cache")
                 ->store($config)
                 ->flush();

        $this->info("✔︎ Entire model cache has been flushed.");

        return 0;
    }

    /**
     * @param string $option
     * @return int
     */
    protected function flushModelCache(string $option) : int {
        $model = new $option;
        $usesCachableTrait = $this->getAllTraitsUsedByClass($option)
                                  ->contains("Knovators\LaravelModelCaching\Traits\Cachable");

        if (!$usesCachableTrait) {
            $this->error("'{$option}' is not an instance of CachedModel.");
            $this->line("Only CachedModel instances can be flushed.");

            return 1;
        }

        $model->flushCache();
        $this->info("✔︎ Cache for model '{$option}' has been flushed.");

        return 0;
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param string $classname
     * @param bool   $autoload
     * @return Collection
     */
    protected function getAllTraitsUsedByClass(
        string $classname,
        bool $autoload = true
    ) : Collection {
        $traits = collect();

        if (class_exists($classname, $autoload)) {
            $traits = collect(class_uses($classname, $autoload));
        }

        $parentClass = get_parent_class($classname);

        if ($parentClass) {
            $traits = $traits
                ->merge($this->getAllTraitsUsedByClass($parentClass, $autoload));
        }

        return $traits;
    }
}
