<?php

namespace Spatie\ModelInfo;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use Spatie\ModelInfo\Attributes\Attribute;
use Spatie\ModelInfo\Attributes\AttributeFinder;
use Spatie\ModelInfo\Relations\Relation;
use Spatie\ModelInfo\Relations\RelationFinder;

class ModelInfo
{
    protected static array $typeMappings = [
        'bit' => 'string',
        'enum' => 'string',
        'geometry' => 'string',
        'geomcollection' => 'string',
        'linestring' => 'string',
        'multilinestring' => 'string',
        'multipoint' => 'string',
        'multipolygon' => 'string',
        'point' => 'string',
        'polygon' => 'string',
        'sysname' => 'string',
    ];

    /**
     * @param  string|null  $directory
     * @param  string|null  $basePath
     * @param  string|null  $baseNamespace
     * @return Collection<ModelInfo>
     */
    public static function forAllModels(
        string $directory = null,
        string $basePath = null,
        string $baseNamespace = null
    ): Collection {
        return ModelFinder::all($directory, $basePath, $baseNamespace)
            ->map(function (string $model) {
                return self::forModel($model);
            });
    }

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

        static::registerTypeMappings($model->getConnection()->getDoctrineSchemaManager()->getDatabasePlatform());

        return new self(
            $model::class,
            (new ReflectionClass($model))->getFileName(),
            $model->getConnection()->getName(),
            $model->getConnection()->getTablePrefix().$model->getTable(),
            RelationFinder::forModel($model),
            AttributeFinder::forModel($model),
            self::getTraits($model),
            self::getExtraModelInfo($model),
        );
    }

    public function __construct(
        public string $class,
        public string $fileName,
        public string $connectionName,
        public string $tableName,
        public Collection $relations,
        public Collection $attributes,
        public Collection $traits,
        public mixed $extra = null,
    ) {
    }

    protected static function getExtraModelInfo(Model $model): mixed
    {
        if (method_exists($model, 'extraModelInfo')) {
            return $model->extraModelInfo();
        }

        return null;
    }

    protected static function getTraits(Model $model): Collection
    {
        return collect(array_values(class_uses($model)));
    }

    public function toArray(): array
    {
        $properties = get_object_vars($this);
        $properties['relations'] = $properties['relations']->toArray();
        $properties['attributes'] = $properties['attributes']->toArray();

        return $properties;
    }

    public function attribute(string $name): ?Attribute
    {
        return $this->attributes->first(
            fn (Attribute $attribute) => $attribute->name === $name
        );
    }

    public function relation(string $name): ?Relation
    {
        return $this->relations->first(
            fn (Relation $relation) => $relation->name === $name
        );
    }

    /**
     * Register the custom Doctrine type mappings available in laravel
     *
     * @param  \Doctrine\DBAL\Platforms\AbstractPlatform  $platform
     * @return void
     *
     * @throws Exception
     *
     * @see \Illuminate\Database\Console\DatabaseInspectionCommand::registerTypeMappings
     */
    protected static function registerTypeMappings(AbstractPlatform $platform): void
    {
        foreach (static::$typeMappings as $type => $value) {
            $platform->registerDoctrineTypeMapping($type, $value);
        }
    }

    public static function addTypeMapping(string $dbType, string $doctrineType): void
    {
        static::$typeMappings[$dbType] = $doctrineType;
    }
}
