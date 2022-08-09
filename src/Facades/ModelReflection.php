<?php

namespace Spatie\ModelReflection\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\ModelReflection\ModelReflection
 */
class ModelReflection extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Spatie\ModelReflection\ModelReflection::class;
    }
}
