<?php

namespace Spatie\ModelInfo\Util;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;

trait RegistersAdditionalTypeMappings
{
    protected static array $typeMappings = [
        'bit' => 'string',
        'enum' => 'string',
        'geometry' => 'string',
        'geomcollection' => 'string',
        'linestring' => 'string',
        'multilinestring' => 'string',
        'multipoint' => 'string',
        'multipolygon' => 'string',
        'point' => 'string',
        'polygon' => 'string',
        'sysname' => 'string',
        'citext' => 'string,
    ];

    /**
     * Register the custom Doctrine type mappings available in laravel
     *
     * @param  \Doctrine\DBAL\Platforms\AbstractPlatform  $platform
     * @return void
     *
     * @throws Exception
     *
     * @see \Illuminate\Database\Console\DatabaseInspectionCommand::registerTypeMappings
     */
    protected static function registerTypeMappings(AbstractPlatform $platform): void
    {
        foreach (static::$typeMappings as $type => $value) {
            $platform->registerDoctrineTypeMapping($type, $value);
        }
    }

    public static function addTypeMapping(string $dbType, string $doctrineType): void
    {
        static::$typeMappings[$dbType] = $doctrineType;
    }
}
