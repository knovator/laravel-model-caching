<?php namespace Knovators\LaravelModelCaching;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Knovators\LaravelModelCaching\Traits\CachePrefixing;

/**
 * Class CacheTags
 * @package knovators\LaravelModelCaching
 */
class CacheTags
{

    use CachePrefixing;

    protected $eagerLoad;
    protected $model;
    protected $query;

    /**
     * CacheTags constructor.
     * @param array $eagerLoad
     * @param       $model
     * @param       $query
     */
    public function __construct(
        array $eagerLoad,
        $model,
        $query
    ) {
        $this->eagerLoad = $eagerLoad;
        $this->model = $model;
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function make() : array {
        $tags = collect($this->eagerLoad)
            ->keys()
            ->map(function ($relationName) {
                $relation = $this->getRelation($relationName);

                return $this->getCachePrefix()
                    . (new Str)->slug(get_class($relation->getQuery()->getModel()));
            })
            ->prepend($this->getTagName())
            ->values()
            ->toArray();

// dump($tags);
        return $tags;
    }

    protected function getRelation(string $relationName) {
        return collect(explode('.', $relationName))
            ->reduce(function ($carry, $name) {
                $carry = $carry ?: $this->model;
                $carry = $this->getRelatedModel($carry);

                return $carry->{$name}();
            });
    }

    /**
     * @param $carry
     * @return Model
     */
    protected function getRelatedModel($carry) {
        if ($carry instanceof Relation) {
            return $carry->getQuery()->getModel();
        }

        return $carry;
    }

    /**
     * @return string
     */
    protected function getTagName() : string {
        return $this->getCachePrefix()
            . (new Str)->slug(get_class($this->model));
    }
}
