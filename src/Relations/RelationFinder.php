<?php

namespace Spatie\ModelInfo\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as IlluminateRelation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionMethod;
use Spatie\ModelInfo\Attributes\Attribute;
use SplFileObject;

class RelationFinder
{
    /** @var array<string> */
    protected array $relationMethods = [
        'hasMany',
        'hasManyThrough',
        'hasOneThrough',
        'belongsToMany',
        'hasOne',
        'belongsTo',
        'morphOne',
        'morphTo',
        'morphMany',
        'morphToMany',
        'morphedByMany',
    ];

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
        return collect(get_class_methods($model))
            ->map(fn ($method) => new ReflectionMethod($model, $method))
            ->reject(fn (ReflectionMethod $method) => $method->isStatic()
                || $method->isAbstract()
                || $method->getDeclaringClass()->getName() !== get_class($model)
            )
            ->filter(function (ReflectionMethod $method) {
                $file = new SplFileObject($method->getFileName());
                $file->seek($method->getStartLine() - 1);
                $code = '';

                while ($file->key() < $method->getEndLine()) {
                    $code .= $file->current();
                    $file->next();
                }

                return collect($this->relationMethods)
                    ->contains(fn ($relationMethod) => str_contains($code, '$this->'.$relationMethod.'('));
            })
            ->map(function (ReflectionMethod $method) use ($model) {
                $relation = $method->invoke($model);

                if (! $relation instanceof IlluminateRelation) {
                    return null;
                }

                return new Relation(
                    $method->getName(),
                    Str::afterLast(get_class($relation), '\\'),
                    get_class($relation->getRelated()),
                );
            });
    }
}
