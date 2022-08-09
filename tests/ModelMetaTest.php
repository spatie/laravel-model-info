<?php

use Spatie\ModelReflection\ModelMeta;
use Spatie\ModelReflection\Tests\TestSupport\Models\RelationTestModel;

it('can get meta information about a model', function () {
    $modelMeta = ModelMeta::forModel(RelationTestModel::class);

    dd($modelMeta);
});
