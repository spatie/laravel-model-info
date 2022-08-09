<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class RelationTestModel extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
