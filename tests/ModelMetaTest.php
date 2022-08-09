<?php

use Spatie\ModelMeta\ModelMeta;
use Spatie\ModelMeta\Tests\TestSupport\Models\RelationTestModel;
use Spatie\ModelMeta\Tests\TestSupport\Models\TestModel;

it('can get meta information about a model', function() {
    $modelMeta = ModelMeta::forModel(RelationTestModel::class);

    dd($modelMeta);
});
