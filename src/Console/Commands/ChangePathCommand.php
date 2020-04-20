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
    protected $mongoQueryBuilder = 'Jenssegers\Mongodb\Query\Builder';
    protected $mongoBuilder = 'Jenssegers\Mongodb\Eloquent\Builder';
    protected $laravelModel = 'Illuminate\Database\Eloquent\Model';
    protected $laravelRelation = 'Illuminate\Database\Eloquent\Relations';
    protected $laravelBuilder = 'Illuminate\Database\Eloquent\Builder';
    protected $laravelQueryBuilder = 'Illuminate\Database\Query\Builder';
    protected $paths = [
        'vendor/knovators/laravel-model-caching/src'
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
                    if ($filename->getFilename() !== "ChangePathCommand.php") {
                        $str = file_get_contents($filename);
                        $str = str_replace($this->laravelModel, $this->mongoModel, $str);
                        $str = str_replace($this->laravelBuilder, $this->mongoBuilder, $str);
                        $str = str_replace($this->laravelRelation, $this->mongoRelation, $str);
                        $str = str_replace($this->laravelQueryBuilder, $this->mongoQueryBuilder,
                            $str);
                        file_put_contents($filename, $str);
                    }
                }
            }
        }
    }
}
