<?php

namespace Spatie\ModelInfo\Relations;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;
use Spatie\ModelInfo\ModelInfo;

class Relation implements Arrayable
{
    use Macroable;

    public function __construct(
        public string $name,
        public string $type,
        public string $related,
    ) {}

    public function relatedModelInfo(): ModelInfo
    {
        return ModelInfo::forModel($this->related);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
