<?php namespace Knovators\LaravelModelCaching\Tests\Fixtures;

use Knovators\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Knovators\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AuthorWithInlineGlobalScope extends Model
{
    use Cachable;
    use SoftDeletes;

    protected $casts = [
        "finances" => "array",
    ];
    protected $fillable = [
        'name',
        'email',
        "finances",
    ];
    protected $table = "authors";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('inlineScope', function (CachedBuilder $builder) {
            return $builder->where('name', 'LIKE', "A%");
        });
    }

    public function books() : HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function printers() : HasManyThrough
    {
        return $this->hasManyThrough(Printer::class, Book::class);
    }

    public function profile() : HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function getLatestBookAttribute()
    {
        return $this
            ->books()
            ->latest("id")
            ->first();
    }

    public function scopeStartsWithA(Builder $query) : Builder
    {
        return $query->where('name', 'LIKE', 'A%');
    }

    public function scopeNameStartsWith(Builder $query, string $startOfName) : Builder
    {
        return $query->where("name", "LIKE", "{$startOfName}%");
    }
}
