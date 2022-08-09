<?php

use Spatie\ModelInfo\ModelInfo;
use Spatie\ModelInfo\Tests\TestSupport\Models\ExtraModelInfoModel;
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

    expect($modelInfo)->toHaveCount(3);
    expect($modelInfo->first())->toBeInstanceOf(ModelInfo::class);
});

it('can get extra info from a model', function () {
    $modelInfo = ModelInfo::forModel(ExtraModelInfoModel::class);

    expect($modelInfo->extra)->toBe('extra info');
});
