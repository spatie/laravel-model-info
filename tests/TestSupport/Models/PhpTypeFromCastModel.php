<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\ModelInfo\Tests\TestSupport\Enums\TestEnum;

class PhpTypeFromCastModel extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'array' => 'array',
            'encryptedCollection' => 'encrypted:collection',
            'enum' => TestEnum::class,
        ];
    }
}
