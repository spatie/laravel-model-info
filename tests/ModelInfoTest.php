<?php

use Spatie\ModelInfo\ModelInfo;
use Spatie\ModelInfo\Tests\TestSupport\Models\RelationTestModel;

it('can get meta information about a model', function () {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    $modelInfo = $modelInfo->toArray();

    $modelInfo['fileName'] = str_replace($this->getTestDirectory(), '', $modelInfo['fileName']);

    $this->assertMatchesSnapshot($modelInfo);
});

it('can get meta information about all models', function () {
    $modelInfo = ModelInfo::forAllModels(
        $this->getTestSupportDirectory(),
        $this->getTestDirectory(),
        "Spatie\ModelInfo\Tests",
    );

    expect($modelInfo)->toHaveCount(2);
    expect($modelInfo->first())->toBeInstanceOf(ModelInfo::class);
});
