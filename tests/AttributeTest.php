<?php

use Illuminate\Support\Collection;
use Spatie\ModelInfo\Attributes\AttributeFinder;
use Spatie\ModelInfo\Tests\TestSupport\Enums\TestEnum;
use Spatie\ModelInfo\Tests\TestSupport\Models\ExtendedTypesModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\PhpTypeFromCastModel;
use Spatie\ModelInfo\Tests\TestSupport\Models\TestModel;

use function Spatie\Snapshots\assertMatchesSnapshot;

it('can get the attributes of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel);

    expect($attributes)->toHaveCount(12);

    matchesAttributesSnapshot($attributes);
});

/**
 * @see https://laravel.com/docs/8.x/eloquent-mutators#defining-an-accessor
 */
it('can get the accessor attributes of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel);

    $titleUppercaseAttr = $attributes->first(fn ($attr) => $attr->name === 'title_uppercase');
    $titleWithoutReturnTypeAttr = $attributes->first(fn ($attr) => $attr->name === 'title_without_return_type');

    expect($titleUppercaseAttr)
        ->cast->toBe('accessor')
        ->not()->toBeNull()
        ->phpType->toBe('string');
    expect($titleWithoutReturnTypeAttr)
        ->cast->toBe('accessor')
        ->not()->toBeNull()
        ->phpType->toBeNull();
});

/**
 * @see https://laravel.com/docs/8.x/eloquent-mutators#defining-a-mutator
 */
it('can get the mutator attributes of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel);

    $passwordMutatorAttr = $attributes->first(fn ($attr) => $attr->name === 'encrypted_password');
    $passwordMutatorWithoutTypeHintAttr = $attributes->first(fn ($attr) => $attr->name === 'trimmed_and_encrypted_password');

    expect($passwordMutatorAttr)
        ->cast->toBe('mutator')
        ->not()->toBeNull()
        ->phpType->toBe('string');
    expect($passwordMutatorWithoutTypeHintAttr)
        ->cast->toBe('mutator')
        ->not()->toBeNull()
        ->phpType->toBeNull();
});

it('can handle accessor-mutator combinations', function () {
    $attributes = AttributeFinder::forModel(new TestModel);

    $dottedNameAttr = $attributes->first(fn ($attr) => $attr->name === 'dotted_name');

    expect($dottedNameAttr)
        ->cast->toBe('attribute')
        ->not()->toBeNull()
        ->phpType->toBe('string');
    expect($attributes->where('name', 'dotted_name')->count())
        ->toBe(1);
});

/**
 * @see https://laravel.com/docs/9.x/eloquent-mutators#accessors-and-mutators
 */
it('can handle virtual attributes of a model', function () {
    $attributes = AttributeFinder::forModel(new TestModel);

    $titleLowercaseAttr = $attributes->first(fn ($attr) => $attr->name === 'title_lowercase');

    expect($titleLowercaseAttr)
        ->cast->toBe('attribute')
        ->not()->toBeNull()
        ->phpType->toBeNull();
});

it('can get extended column types for a model', function () {
    $attributes = AttributeFinder::forModel(new ExtendedTypesModel);

    expect($attributes)->toHaveCount(6);

    matchesAttributesSnapshot($attributes);
});

it('retrieves phpType attribute from cast and falls back to column type', function () {
    $attributes = AttributeFinder::forModel(new PhpTypeFromCastModel);

    expect($attributes->pluck('phpType')->toArray())->toBe([
        'array',
        '\\'.Collection::class,
        '\\'.TestEnum::class,
        'int',
    ]);
});

function matchesAttributesSnapshot(Collection $attributes)
{
    $attributes = $attributes->map->toArray();

    $attributes = $attributes->toArray();

    assertMatchesSnapshot($attributes);
}
