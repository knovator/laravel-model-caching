<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Knovators\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use Cachable;

    protected $fillable = [
        'description',
        'subject',
        "commentable_id",
        "commentable_type",
    ];

    public function commentable() : MorphTo
    {
        return $this->morphTo();
    }
}
