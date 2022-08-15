<?php

use Illuminate\Support\Collection;
use Spatie\ModelInfo\Attributes\AttributeFinder;
use Spatie\ModelInfo\Tests\TestSupport\Models\TestModel;
use function Spatie\Snapshots\assertMatchesSnapshot;

it('can get the attributes of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel());

    expect($attributes)->toHaveCount(6);

    matchesAttributesSnapshot($attributes);
});

it('can get virtual attribute php types of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel());

    $titleUppercaseAttr = $attributes->first(fn ($attr) => $attr->name === 'title_uppercase');
    $titleWithoutReturnTypeAttr = $attributes->first(fn ($attr) => $attr->name === 'title_without_return_type');

    $this->assertNotNull($titleUppercaseAttr);
    $this->assertEquals('string', $titleUppercaseAttr->phpType);
    $this->assertEquals(null, $titleWithoutReturnTypeAttr->phpType);
});

function matchesAttributesSnapshot(Collection $attributes)
{
    $attributes = $attributes->map->toArray();

    $attributes = $attributes->toArray();

    assertMatchesSnapshot($attributes);
}
