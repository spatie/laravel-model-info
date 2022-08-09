<?php

namespace Spatie\ModelMeta\Relations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;

class Relation implements Arrayable
{
    use Macroable;

    public function __construct(
        public string $name,
        public string $type,
        public string $related,
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
