<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    public function getTitleUppercaseAttribute(): string
    {
        return strtoupper($this->title);
    }

    public function getTitleWithoutReturnTypeAttribute()
    {
        return $this->title;
    }
}
