<?php

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;
use Spatie\ModelInfo\ModelInfo;
use Spatie\ModelInfo\Relations\Relation;
use Spatie\ModelInfo\Relations\RelationFinder;
use Spatie\ModelInfo\Tests\TestSupport\Models\RelationTestModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\TestModel;

it('can find the relations on a model', function () {
    $relations = RelationFinder::forModel(new RelationTestModel);

    expect($relations)->toHaveCount(1);
});

it('will find no relations on a model that has none', function () {
    $relations = RelationFinder::forModel(new TestModel);

    expect($relations)->toHaveCount(0);
});

it('can get the model info of the related model', function () {
    /** @var Collection<Relation> $relations */
    $relations = RelationFinder::forModel(new RelationTestModel);

    /** @var ModelInfo $modelInfo */
    $modelInfo = $relations->first()->relatedModelInfo();

    expect($modelInfo)->toBeInstanceOf(ModelInfo::class);
    expect($modelInfo->class)->toBe(User::class);
});
