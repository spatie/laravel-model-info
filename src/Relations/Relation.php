<?php

namespace Spatie\ModelReflection\Relations;

use Illuminate\Contracts\Support\Arrayable;

class Relation implements Arrayable
{
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
