<?php

namespace Spatie\ModelMeta;

use Error;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use ReflectionClass;
use SplFileInfo;

class ModelFinder
{
    /** @return Collection<class-string<Model>> */
    public static function all(
        string $directory = null,
        string $basePath = null,
        string $baseNamespace = null,
    ): Collection {
        $directory ??= app_path();
        $basePath ??= $basePath;

        $globPattern = realpath($directory).'/**/*.php';

        return collect(File::glob($globPattern))
            ->map(fn (string $class) => new SplFileInfo($class))
            ->map(fn (SplFileInfo $file) => self::fullQualifiedClassNameFromFile($file, $basePath, $baseNamespace))
            ->map(function (string $class) {
                try {
                    return new ReflectionClass($class);
                } catch (Exception | Error $e) {
                    return null;
                }
            })
            ->filter()
            ->filter(fn (ReflectionClass $class) => $class->isSubclassOf(Model::class))
            ->map(fn (ReflectionClass $reflectionClass) => $reflectionClass->getName());
    }

    protected static function fullQualifiedClassNameFromFile(
        SplFileInfo $file,
        string $basePath,
        string $baseNamespace
    ): string {
        return Str::of($file->getRealPath())
            ->replaceFirst($basePath, '')
            ->replaceLast('.php', '')
            ->trim(DIRECTORY_SEPARATOR)
            ->ucfirst()
            ->replace(
                [DIRECTORY_SEPARATOR, 'App\\'],
                ['\\', app()->getNamespace()],
            )
            ->prepend($baseNamespace.'\\');
    }
}
