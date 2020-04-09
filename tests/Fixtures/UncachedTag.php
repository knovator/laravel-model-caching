<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Knovators\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class UncachedTag extends Model
{
    protected $fillable = [
        "name",
    ];
    protected $table = "tags";

    public function posts() : MorphToMany
    {
        return $this->morphedByMany(UncachedPost::class, "taggable");
    }
}
