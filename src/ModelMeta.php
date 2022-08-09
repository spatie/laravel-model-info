<?php

namespace Spatie\ModelMeta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\ModelMeta\Attributes\AttributeFinder;
use Spatie\ModelMeta\Relations\RelationFinder;

class ModelMeta
{
    /**
     * @param  class-string<Model>|Model|ReflectionClass  $model
     * @return self
     */
    public static function forModel(string|Model|ReflectionClass $model): self
    {
        if ($model instanceof ReflectionClass) {
            $model = $model->getName();
        }

        if (is_string($model)) {
            $model = new $model;
        }

        return new self(
            $model::class,
            (new ReflectionClass($model))->getFileName(),
            $model->getConnection()->getName(),
            $model->getConnection()->getTablePrefix().$model->getTable(),
            RelationFinder::forModel($model),
            AttributeFinder::forModel($model),
        );
    }

    public function __construct(
        public string $class,
        public string $fileName,
        public string $connectionName,
        public string $tableName,
        public Collection $relations,
        public Collection $attributes,
    ) {
    }

    public function toArray(): array
    {
        $properties = get_object_vars($this);
        $properties['relations'] = $this->itemsToArray($properties['relations']);
        $properties['attributes'] = $this->itemsToArray($properties['attributes']);

        return $properties;
    }

    protected function itemsToArray(Collection $items): array
    {
        /** @var Collection $items */
        $items = $items->map->toArray();

        return $items->toArray();
    }
}
