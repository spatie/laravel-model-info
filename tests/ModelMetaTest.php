<?php

use Spatie\ModelMeta\ModelMeta;
use Spatie\ModelMeta\Tests\TestSupport\Models\RelationTestModel;

it('can get meta information about a model', function () {
    $modelMeta = ModelMeta::forModel(RelationTestModel::class);

    $this->assertMatchesSnapshot($modelMeta->toArray());
});
