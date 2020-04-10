<?php

namespace Knovators\LaravelModelCaching\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;


/**
 * Class ChangePathCommand
 * @package App\Console\Commands
 */
class ChangePathCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:caching_mongodb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $mongoModel = 'Jenssegers\Mongodb\Eloquent\Model';
    protected $mongoRelation = 'Jenssegers\Mongodb\Relations';
    protected $mongoBuilder = 'Jenssegers\Mongodb\Query\Builder';
    protected $laravelModel = 'Illuminate\Database\Eloquent\Model';
    protected $laravelRelation = 'Illuminate\Database\Eloquent\Model';
    protected $laravelBuilder = 'Illuminate\Database\Eloquent\Builder';
    protected $paths = [
        'vendor/knovators/laravel-model-caching/src',
        'vendor/knovators/laravel-model-caching/src/Relations',
        'vendor/knovators/laravel-model-caching/src/Traits',
        'vendor/knovators/laravel-model-caching/src/Traits/LaravelPivotEvents',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        foreach ($this->paths as $path) {
            if (File::isDirectory($path)) {
                $files = File::allfiles($path);
                foreach ($files as $filename) {
                    if (strpos('Commands', $filename->getRealPath()) !== false) {
                        $str = file_get_contents($filename);
                        $str = str_replace($this->laravelModel, $this->mongoModel, $str);
                        $str = str_replace($this->laravelBuilder, $this->mongoBuilder, $str);
                        $str = str_replace($this->laravelRelation, $this->mongoRelation, $str);
                        file_put_contents($filename, $str);
                    }
                }
            }
        }
    }
}
