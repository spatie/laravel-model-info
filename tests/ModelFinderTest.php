<?php

namespace Spatie\ModelInfo\Tests;

use Spatie\ModelInfo\ModelFinder;
use Spatie\ModelInfo\Tests\TestSupport\Models\ExtraModelInfoModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\Nested\Model\NestedModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\RelationTestModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\TestModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\TraitTestModel;

it('can discover all models in a directory', function () {
    $models = ModelFinder::all(
        $this->getTestSupportDirectory(),
        $this->getTestDirectory(),
        "Spatie\ModelInfo\Tests",
    );

    expect($models)->toHaveCount(5);

    expect($models->toArray())->toEqualCanonicalizing([
        NestedModel::class,
        ExtraModelInfoModel::class,
        RelationTestModel::class,
        TestModel::class,
        TraitTestModel::class,
    ]);
});
