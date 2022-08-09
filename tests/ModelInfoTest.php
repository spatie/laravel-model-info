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

it('can get a specific attribute', function() {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    $attribute = $modelInfo->attribute('name');
    expect($attribute->name)->toBe('name');
});

it('it will return null when getting a non-existing attribute', function() {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

   expect($modelInfo->attribute('does_not_exist'))->toBeNull();
});

it('can get a specific relation', function() {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    $relation = $modelInfo->relation('user');

    expect($relation->name)->toBe('user');
});

it('it will return null when getting a non-existing relation', function() {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    expect($modelInfo->relation('doesNotExist'))->toBeNull();
});
