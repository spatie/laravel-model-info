<?php

namespace Spatie\ModelInfo\Tests;

use ReflectionClass;
use Spatie\ModelInfo\ModelFinder;
use Spatie\ModelInfo\Tests\TestSupport\Models\RelationTestModel;

it('can discover all models in a directory', function () {
    $models = ModelFinder::all(
        $this->getTestSupportDirectory(),
        $this->getTestDirectory(),
        "Spatie\ModelInfo\Tests",
    );

    expect($models)->toHaveCount(2);

    /** @var ReflectionClass $firstModel */
    $firstModel = $models->first();

    expect($firstModel)->toBe(RelationTestModel::class);
});
