<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Knovators\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use Cachable;

    protected $fillable = [
        'path',
    ];

    public function imagable() : MorphTo
    {
        return $this->morphTo();
    }
}
