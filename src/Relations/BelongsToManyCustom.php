<?php

namespace Knovators\LaravelModelCaching\Relations;

use Knovators\LaravelModelCaching\Traits\LaravelPivotEvents\FiresPivotEventsTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class BelongsToManyCustom
 * @package Knovators\LaravelModelCaching\Relations
 */
class BelongsToManyCustom extends BelongsToMany
{

    use FiresPivotEventsTrait;
}
