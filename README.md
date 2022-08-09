
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# Get information about the models in your Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-model-info.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-model-info)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-model-info/run-tests?label=tests)](https://github.com/spatie/laravel-model-info/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/spatie/laravel-model-info/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/spatie/laravel-model-info/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-model-info.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-model-info)

Using this package you can determine which attributes and relations your model classes have.

```php
use Spatie\ModelInfo\ModelInfo;

$modelInfo = ModelInfo::forModel(YourModel::class);

$modelInfo->fileName; // returns the filename that contains your model
$modelInfo->tableName; // returns the name of the table your models are stored in
$modelInfo->attributes; // returns a collection of `Attribute` objects
$modelInfo->relations; // returns a collection of `Relation` objects
```

Here's how you can get information about the attributes:

```php
$modelInfo->attributes->first()->name; // returns the name of the first attribute
$modelInfo->attributes->first()->type; // returns the type of the first attribute (string, integer, ...)
```

Here's how you can get information about the relations

```php
// returns the name of the first relation, eg. `author`
$modelInfo->attributes->first()->name;

// returns the type of the
// first relation, eg. `BelongsTo`
$modelInfo->attributes->first()->type;

// returns the related model of the
// first relation, eg. `App\Models\User`
$modelInfo->attributes->first()->related; 
```

Additionally, the package can also discover all the models in your application.

```php
$models = ModelFinder::all(); // returns a `Illuminate\Support\Collection` containing all the class names of all your models.
```

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-model-info.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-model-info)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-model-info
```

## Usage

You can get information about a model by calling `forModel`:

```php
use Spatie\ModelInfo\ModelInfo;

$modelInfo = ModelInfo::forModel(YourModel::class);

$modelInfo->fileName; // returns the filename that contains your model
$modelInfo->tableName; // returns the name of the table your models are stored in
$modelInfo->attributes; // returns a collection of `Attribute` objects
$modelInfo->relations; // returns a collection of `Relation` objects
```

### Attributes

A `Spatie\ModelInfo\Attributes\Attribute` object has these properties:

- `name`
- `type`
- `increments`
- `nullable`
- `default`
- `unique`
- `fillable`
- `appended`
- `cast`
- `virtual`

### Relationships

A `Spatie\ModelInfo\Relations\Relation` object has these properties:

- `name`
- `type`
- `related`

## Discovering all models in your application

```php
use Spatie\ModelInfo\ModelFinder;

// returns a `Illuminate\Support\Collection` containing
// all the class names of all your models.
$models = ModelFinder::all(); 
```

## Getting information on all model in your application

The `ModelInfo` class can get information about all models in your application

```php
use Spatie\ModelInfo\ModelInfo;

ModelInfo::forAllModels(); // returns a collection of `ModelInfo` instances
```

## Adding extra info on a model

To add extra info on a model, add a method `extraModelInfo` to your model. It can return anything you want: an string, an object, an array.

```php
// in your model

public function extraModelInfo()
{
    return 'anything you want';
}
```

The returned value will be available on the `extra` property of a `ModelInfo` instance.

```php
use Spatie\ModelInfo\ModelInfo;

$modelInfo = \Spatie\ModelInfo\ModelInfo::forModel(YourModel::class);

$modelInfo->extra; // returns 'anything you want'
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/freekmurze/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

This package contains code taken from the `model:show` command of [Laravel](https://github.com/laravel/framework).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
