<?php

namespace Spatie\ModelInfo\Attributes;

use BackedEnum;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\DecimalType;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Spatie\ModelInfo\Util\RegistersAdditionalTypeMappings;
use UnitEnum;

class AttributeFinder
{
    use RegistersAdditionalTypeMappings;

    /**
     * @param  class-string<Model>|Model  $model
     * @return \Illuminate\Support\Collection<Attribute>
     */
    public static function forModel(string|Model $model): Collection
    {
        if (is_string($model)) {
            $model = new $model;
        }

        return (new self())->attributes($model);
    }

    /**
     * @return \Illuminate\Support\Collection<Attribute>
     */
    protected function attributes(Model $model): Collection
    {
        $schema = $model->getConnection()->getSchemaBuilder();
        $table = $model->getConnection()->getTablePrefix().$model->getTable();

       // static::registerTypeMappings($schema->getDatabasePlatform());

        $columns = $schema->getColumns($table);
        $indexes = $schema->getIndexes($table);

        return collect($columns)
            ->values()
            ->map(function (array $column) use ($model, $indexes) {
                $columnIndexes = $this->getIndexes($column['name'], $indexes);

                return new Attribute(
                    name: $column['name'],
                    phpType: $column['type'],
                    type: $column['type_name'],
                    increments: $column['auto_increment'],
                    nullable: $column['nullable'],
                    default: $column['default'],
                    primary: $columnIndexes->contains(fn (array $index) => $index['primary']),
                    unique: $columnIndexes->contains(fn (array $index) => $index['unique']),
                    fillable: $model->isFillable($column['name']),
                    appended: null,
                    cast: $this->getCastType($column['name'], $model),
                    virtual: false,
                    hidden: $this->attributeIsHidden($column['name'], $model)
                );
            })
            ->merge($this->getVirtualAttributes($model, $columns));
    }

    protected function getColumnType(Column $column): string
    {
        $name = $column->getType()->getName();

        $unsigned = $column->getUnsigned() ? ' unsigned' : '';

        $details = match (get_class($column->getType())) {
            DecimalType::class => $column->getPrecision().','.$column->getScale(),
            default => $column->getLength(),
        };

        if ($details) {
            return "{$name}({$details}){$unsigned}";
        }

        return "{$name}{$unsigned}";
    }

    /**
     * Returns php type name as a string
     * Mappings are defined based on this doctrine documentation:
     *
     * @link https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#detection-of-database-types
     */
    protected function getPhpType(Column $column): string
    {
        $name = $column->getType()->getName();

        return match ($name) {
            'string', 'ascii_string', 'bigint', 'decimal', 'text', 'guid' => 'string',
            'integer', 'smallint' => 'int',
            'float' => 'float',
            'binary', 'blob' => 'resource',
            'boolean' => 'bool',
            'date', 'datetime', 'datetimetz', 'time' => 'DateTime',
            'array' => 'array',
            'json' => 'mixed',
            'object' => 'object',
            default => throw new Exception("Unknown type: $name. Mappings were defined from the Doctrine DBAL documentation at: https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#detection-of-database-types. Double check everything."),
        };
    }

    protected function getColumnDefault(Column $column, Model $model): mixed
    {
        $attributeDefault = $model->getAttributes()[$column->getName()] ?? null;

        return match (true) {
            $attributeDefault instanceof BackedEnum => $attributeDefault->value,
            $attributeDefault instanceof UnitEnum => $attributeDefault->name,
            default => $attributeDefault ?? $column->getDefault(),
        };
    }

    /**
     * @param  Index[]  $indexes
     * @return Collection<int, Index>
     */
    protected function getIndexes(string $column, array $indexes)
    {
        return collect($indexes)
            ->filter(fn (array $index) => count($index['columns']) === 1 && $index['columns'][0] === $column);
    }

    protected function attributeIsHidden(string $attribute, Model $model): bool
    {
        if (count($model->getHidden()) > 0) {
            return in_array($attribute, $model->getHidden());
        }

        if (count($model->getVisible()) > 0) {
            return ! in_array($attribute, $model->getVisible());
        }

        return false;
    }

    protected function getCastType(string $column, Model $model): ?string
    {
        if ($model->hasGetMutator($column) || $model->hasSetMutator($column)) {
            return 'accessor';
        }

        if ($model->hasAttributeMutator($column)) {
            return 'attribute';
        }

        return $this->getCastsWithDates($model)->get($column) ?? null;
    }

    protected function getCastsWithDates(Model $model): Collection
    {
        return collect($model->getDates())
            ->whereNotNull()
            ->flip()
            ->map(fn () => 'datetime')
            ->merge($model->getCasts());
    }

    /**
     * @param  array<Column>  $columns
     * @return Collection<Attribute>
     */
    protected function getVirtualAttributes(Model $model, array $columns): Collection
    {
        $class = new ReflectionClass($model);

        return collect($class->getMethods())
            ->reject(
                fn (ReflectionMethod $method) => $method->isStatic()
                    || $method->isAbstract()
                    || $method->getDeclaringClass()->getName() !== get_class($model)
            )
            ->map(function (ReflectionMethod $method) use ($model) {
                if (preg_match('/(?<=^|;)get([^;]+?)Attribute(;|$)/', $method->getName(), $matches) === 1) {
                    return [
                        'name' => Str::snake($matches[1]),
                        'cast_type' => 'accessor',
                        'php_type' => $method->getReturnType()?->getName(),
                    ];
                }

                if (preg_match('/(?<=^|;)set([^;]+?)Attribute(;|$)/', $method->getName(), $matches) === 1) {
                    return [
                        'name' => Str::snake($matches[1]),
                        'cast_type' => 'mutator',
                        'php_type' => collect($method->getParameters())->firstWhere('name', 'value')?->getType()?->__toString(),
                    ];
                }

                if ($model->hasAttributeMutator($method->getName())) {
                    return [
                        'name' => Str::snake($method->getName()),
                        'cast_type' => 'attribute',
                        'php_type' => null,
                    ];
                }

                return [];
            })
            ->reject(fn ($cast) => ! isset($cast['name']) || collect($columns)->has($cast['name']))
            ->map(fn ($cast) => new Attribute(
                name: $cast['name'],
                phpType: $cast['php_type'] ?? null,
                type: null,
                increments: false,
                nullable: null,
                default: null,
                primary: null,
                unique: null,
                fillable: $model->isFillable($cast['name']),
                appended: $model->hasAppended($cast['name']),
                cast: $cast['cast_type'],
                virtual: true,
                hidden: $this->attributeIsHidden($cast['name'], $model)
            ))
            // Convert duplicate entries for accessor-mutator combinations
            ->groupBy('name')
            ->flatMap(function (Collection $items) {
                if ($items->count() !== 2) {
                    return $items;
                }

                if (! $items->firstWhere('cast', 'accessor') || ! $items->firstWhere('cast', 'mutator')) {
                    return $items;
                }

                $attribute = $items->first();
                $attribute->phpType = $items[0]->phpType ?? $items[1]->phpType;
                $attribute->cast = 'attribute';

                return [$attribute];
            });
    }
}
