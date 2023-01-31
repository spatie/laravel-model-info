<?php

namespace Spatie\ModelInfo\Facades;

use Illuminate\Support\Facades\Facade;
use Spatie\ModelInfo\ModelInfo;

class ModelInfoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ModelInfo::class;
    }
}
