<?php

namespace Knovators\LaravelModelCaching\Traits\LaravelPivotEvents;

use Knovators\LaravelModelCaching\Relations\BelongsToManyCustom;
use Knovators\LaravelModelCaching\Relations\MorphToManyCustom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait ExtendRelationsTrait
 * @package Knovators\LaravelModelCaching\Traits\LaravelPivotEvents
 */
trait ExtendRelationsTrait
{

    protected function newMorphToMany(
        $query,
        $parent,
        $name,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null,
        $inverse = false
    ) {
        return new MorphToManyCustom($query, $parent, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey,
            $relationName, $inverse);
    }

    protected function newBelongsToMany(
        $query,
        $parent,
        $table,
        $foreignPivotKey,
        $relatedPivotKey,
        $parentKey,
        $relatedKey,
        $relationName = null
    ) {
        return new BelongsToManyCustom($query, $parent, $table, $foreignPivotKey, $relatedPivotKey,
            $parentKey, $relatedKey, $relationName);
    }
}
