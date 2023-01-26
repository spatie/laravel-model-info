<?php

namespace Spatie\ModelInfo\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as IlluminateRelation;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use Spatie\ModelInfo\Attributes\Attribute;

class RelationFinder
{
    /**
     * @param  class-string<Model>|Model  $model
     * @return Collection<Attribute>
     */
    public static function forModel(string|Model $model): Collection
    {
        if (is_string($model)) {
            $model = new $model;
        }

        return (new self())->relations($model);
    }

    /**
     * @param  Model  $model
     * @return Collection<Relation>
     */
    public function relations(Model $model): Collection
    {
        $class = new ReflectionClass($model);

        return collect($class->getMethods())
            ->filter(fn (ReflectionMethod $method) => $this->hasRelationReturnType($method))
            ->map(function (ReflectionMethod $method) use ($model) {
                /** @var \Illuminate\Database\Eloquent\Relations\BelongsTo $relation */
                try {
                    $relation = $method->invoke($model);
                } catch (\Throwable $e) {
                    return;
                }

                return new Relation(
                    $method->getName(),
                    $method->getReturnType(),
                    $relation->getRelated()::class,
                );
            })
            ->filter(fn ($relation) => $relation instanceof Relation);
    }

    protected function hasRelationReturnType(
        ReflectionMethod $method)
    {
        if ($method->getReturnType() instanceof ReflectionNamedType) {
            $returnType = $method->getReturnType()->getName();

            return is_a($returnType, IlluminateRelation::class, true);
        }

        if ($method->getReturnType() instanceof ReflectionUnionType) {
            foreach ($method->getReturnType()->getTypes() as $type) {
                $returnType = $type->getName();

                if (is_a($returnType, IlluminateRelation::class, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
