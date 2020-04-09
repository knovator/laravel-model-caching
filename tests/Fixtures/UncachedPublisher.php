<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UncachedPublisher extends Model
{
    protected $fillable = [
        'name',
    ];
    protected $table = 'publishers';

    public function books() : HasMany
    {
        return $this->hasMany(Book::class, 'publisher_id', 'id');
    }
}
