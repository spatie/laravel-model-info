<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelInfo\Tests\TestSupport\Traits\TestTrait;

class TraitTestModel extends Model
{
    use TestTrait;
}
