<?php

namespace Spatie\ModelInfo\Attributes;

use BackedEnum;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Types\DecimalType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use UnitEnum;

class AttributeFinder
{
    /**
     * @param class-string<Model>|Model $model
     *
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
     * @param Model $model
     *
     * @return \Illuminate\Support\Collection<Attribute>
     */
    protected function attributes(Model $model): Collection
    {
        $schema = $model->getConnection()->getDoctrineSchemaManager();
        $table = $model->getConnection()->getTablePrefix() . $model->getTable();
        $columns = $schema->listTableColumns($table);
        $indexes = $schema->listTableIndexes($table);

        return collect($columns)
            ->values()
            ->map(fn(Column $column) => new Attribute(
                $column->getName(),
                $this->getColumnType($column),
                $column->getAutoincrement(),
                !$column->getNotnull(),
                $this->getColumnDefault($column, $model),
                $this->columnIsUnique($column->getName(), $indexes),
                $model->isFillable($column->getName()),
                null,
                $this->getCastType($column->getName(), $model),
                false,
            ))
            ->merge($this->getVirtualAttributes($model, $columns));
    }

    protected function getColumnType(Column $column): string
    {
        $name = $column->getType()->getName();

        $unsigned = $column->getUnsigned() ? ' unsigned' : '';

        $details = match (get_class($column->getType())) {
            DecimalType::class => $column->getPrecision() . ',' . $column->getScale(),
            default => $column->getLength(),
        };

        if ($details) {
            return sprintf('%s(%s)%s', $name, $details, $unsigned);
        }

        return sprintf('%s%s', $name, $unsigned);
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
     * @param string $column
     * @param array<Index> $indexes
     *
     * @return bool
     */
    protected function columnIsUnique(string $column, array $indexes): bool
    {
        return collect($indexes)
            ->filter(fn(Index $index) => count($index->getColumns()) === 1 && $index->getColumns()[0] === $column)
            ->contains(fn(Index $index) => $index->isUnique());
    }

    protected function attributeIsHidden(string $attribute, Model $model): bool
    {
        if (count($model->getHidden()) > 0) {
            return in_array($attribute, $model->getHidden());
        }

        if (count($model->getVisible()) > 0) {
            return !in_array($attribute, $model->getVisible());
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
            ->flip()
            ->map(fn() => 'datetime')
            ->merge($model->getCasts());
    }

    /**
     * @param Model $model
     * @param array<Column> $columns
     *
     * @return Collection<Attribute>
     */
    protected function getVirtualAttributes(Model $model, array $columns): Collection
    {
        $class = new ReflectionClass($model);

        return collect($class->getMethods())
            ->reject(fn(ReflectionMethod $method) => $method->isStatic()
                || $method->isAbstract()
                || $method->getDeclaringClass()->getName() !== get_class($model)
            )
            ->mapWithKeys(function (ReflectionMethod $method) use ($model) {
                if (preg_match('/^get(.*)Attribute$/', $method->getName(), $matches) === 1) {
                    return [Str::snake($matches[1]) => 'accessor'];
                }

                if ($model->hasAttributeMutator($method->getName())) {
                    return [Str::snake($method->getName()) => 'attribute'];
                }

                return [];
            })
            ->reject(fn($cast, $name) => collect($columns)->has($name))
            ->map(fn($cast, $name) => new Attribute(
                $name,
                null,
                false,
                null,
                null,
                null,
                $model->isFillable($name),
                $model->hasAppended($name),
                $cast,
                true,

            ))
            ->values();
    }
}
