<?php

use Spatie\ModelMeta\ModelMeta;
use Spatie\ModelMeta\Tests\TestSupport\Models\RelationTestModel;

it('can get meta information about a model', function () {
    $modelMeta = ModelMeta::forModel(RelationTestModel::class);

    $modelMeta = $modelMeta->toArray();

    $modelMeta['fileName'] = str_replace($this->getTestDirectory(), '', $modelMeta['fileName']);

    $this->assertMatchesSnapshot($modelMeta);
});

it('can get meta information about all models', function () {
    $modelMeta = ModelMeta::forAllModels(
        $this->getTestSupportDirectory(),
        $this->getTestDirectory(),
        "Spatie\ModelMeta\Tests",
    );

    expect($modelMeta)->toHaveCount(2);
    expect($modelMeta->first())->toBeInstanceOf(ModelMeta::class);
});
