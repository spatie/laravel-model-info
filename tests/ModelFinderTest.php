<?php

namespace Spatie\ModelMeta\Tests;

use ReflectionClass;
use Spatie\ModelMeta\ModelFinder;
use Spatie\ModelMeta\Tests\TestSupport\Models\RelationTestModel;

it('can discover all models in a directory', function () {
    $modelFinder = new ModelFinder();

    $models = $modelFinder->getModelClasses(
        $this->getTestSupportDirectory(),
        $this->getTestDirectory(),
        "Spatie\ModelMeta\Tests",
    );

    expect($models)->toHaveCount(2);

    /** @var ReflectionClass $firstModel */
    $firstModel = $models->first();

    expect($firstModel)->toBeInstanceOf(ReflectionClass::class);
    expect($firstModel->getName())->toBe(RelationTestModel::class);
});
