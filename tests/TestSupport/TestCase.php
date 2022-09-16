<?php

namespace Spatie\ModelInfo\Tests\TestSupport;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Snapshots\MatchesSnapshots;

class TestCase extends Orchestra
{
    use MatchesSnapshots;

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        Schema::create('test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('relation_test_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('extended_types_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('enum', ['value1', 'value2']);
            $table->multiPolygon('multiPolygon');
            $table->point('point');
            $table->time('time');
        });
    }

    public function getTestSupportDirectory(string $suffix = ''): string
    {
        return __DIR__.$suffix;
    }

    public function getTestDirectory(): string
    {
        return realpath($this->getTestSupportDirectory('/..'));
    }
}
