<?php

namespace  Knovators\LaravelModelCaching\Relations;

use  Knovators\LaravelModelCaching\Traits\LaravelPivotEvents\FiresPivotEventsTrait;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class MorphToManyCustom
 * @package Knovators\LaravelModelCaching\Relations
 */
class MorphToManyCustom extends MorphToMany
{
    use FiresPivotEventsTrait;
}
