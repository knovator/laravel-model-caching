<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Knovators\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Printer extends Model
{
    use Cachable;

    protected $fillable = [
        "book_id",
        'name',
    ];

    public function book() : BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
