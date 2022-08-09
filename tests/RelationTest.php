<?php

use Spatie\ModelReflection\Relations\RelationFinder;
use Spatie\ModelReflection\Tests\TestSupport\Models\RelationTestModel;
use Spatie\ModelReflection\Tests\TestSupport\Models\TestModel;

it('can find the relations on a model', function () {
    $relations = RelationFinder::forModel(new RelationTestModel());

    expect($relations)->toHaveCount(1);
});

it('will find no relations on a model that has none', function () {
    $relations = RelationFinder::forModel(new TestModel());

    expect($relations)->toHaveCount(0);
});
