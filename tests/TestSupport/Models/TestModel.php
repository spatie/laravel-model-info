<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    protected $hidden = [
        'password',
    ];

    public function getTitleUppercaseAttribute(): string
    {
        return strtoupper($this->title);
    }

    public function getTitleWithoutReturnTypeAttribute()
    {
        return $this->title;
    }

    public function setEncryptedPasswordAttribute(string $value)
    {
        $this->password = bcrypt($value);
    }

    public function setTrimmedAndEncryptedPasswordAttribute($value)
    {
        $this->password = bcrypt(trim($value));
    }

    public function titleLowercase(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtolower($value)
        );
    }

    public function getDottedNameAttribute(): string
    {
        return str_replace(' ', '.', $this->name);
    }

    public function setDottedNameAttribute(string $value)
    {
        $this->name = str_replace('.', ' ', $value);
    }

    public function setAttribute($key, $value)
    {
        parent::setAttribute($key, $value);
    }

    public function getAttribute($key)
    {
        parent::getAttribute($key);
    }
}
