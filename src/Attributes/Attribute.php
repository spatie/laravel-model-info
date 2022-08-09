<?php

namespace Spatie\ModelInfo\Attributes;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;

class Attribute implements Arrayable
{
    use Macroable;

    public function __construct(
        public string $name,
        public string $type,
        public bool $increments,
        public bool $nullable,
        public mixed $default,
        public bool $unique,
        public bool $fillable,
        public ?bool $appended,
        public ?string $cast,
        public bool $virtual,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
