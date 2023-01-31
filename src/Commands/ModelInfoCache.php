<?php

namespace Spatie\ModelInfo\Commands;

use Illuminate\Console\Command;
use Spatie\ModelInfo\ModelInfo;

class ModelInfoCache extends Command
{
    protected $signature = 'model-info:cache';

    protected $description = 'Reset the model info cache';

    public function handle()
    {
        if (ModelInfo::forAllModels()->count()) {
            $this->info('Model info cached.');
        } else {
            $this->error('Unable to cache Model info.');
        }
    }
}
