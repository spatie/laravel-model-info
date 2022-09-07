<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;

class RelationTestModel extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
