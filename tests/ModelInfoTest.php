<?php

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use Spatie\ModelInfo\ModelInfo;
use Spatie\ModelInfo\Tests\TestSupport\Models\ExtraModelInfoModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\RelationTestModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\TraitTestModel;
use Spatie\ModelInfo\Tests\TestSupport\Traits\TestTrait;

it('can get meta information about a model', function () {
    ModelInfo::addTypeMapping('time', 'datetime');
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

    expect($modelInfo)->toHaveCount(6);
    expect($modelInfo->first())->toBeInstanceOf(ModelInfo::class);
});

it('can get extra info from a model', function () {
    $modelInfo = ModelInfo::forModel(ExtraModelInfoModel::class);

    expect($modelInfo->extra)->toBe('extra info');
});

it('can get traits from a model', function () {
    $modelInfo = ModelInfo::forModel(TraitTestModel::class);

    expect($modelInfo->traits->first())->toBe(TestTrait::class);
});

it('can get a specific attribute', function () {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    $attribute = $modelInfo->attribute('name');

    expect($attribute->name)->toBe('name');
});

it('it will return null when getting a non-existing attribute', function () {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    expect($modelInfo->attribute('does_not_exist'))->toBeNull();
});

it('can get a specific relation', function () {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    $relation = $modelInfo->relation('user');

    expect($relation)
        ->name->toBe('user')
        ->type->toBe(BelongsTo::class)
        ->related->toBe(User::class);

    $relatedModelInfo = $relation->relatedModelInfo();
    expect($relatedModelInfo)->toBeInstanceOf(ModelInfo::class);
    expect($relatedModelInfo->class)->toBe(User::class);
});

it('it will return null when getting a non-existing relation', function () {
    $modelInfo = ModelInfo::forModel(RelationTestModel::class);

    expect($modelInfo->relation('doesNotExist'))->toBeNull();
});
