<?php

namespace Spatie\ModelInfo;

use Illuminate\Support\ServiceProvider;
use Spatie\ModelInfo\Commands\ModelInfoCache;
use Spatie\ModelInfo\Commands\ModelInfoCacheReset;

class ModelInfoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/model-info.php' => config_path('model-info.php'),
        ], 'model-info-config');

        $this->commands([
            ModelInfoCache::class,
            ModelInfoCacheReset::class,
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/model-info.php', 'model-info');
    }
}
