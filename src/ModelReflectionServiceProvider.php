<?php

namespace Spatie\ModelReflection;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\ModelReflection\Commands\ModelReflectionCommand;

class ModelReflectionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-model-reflection')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-model-reflection_table')
            ->hasCommand(ModelReflectionCommand::class);
    }
}
