<?php

namespace Spatie\ModelReflection\Commands;

use Illuminate\Console\Command;

class ModelReflectionCommand extends Command
{
    public $signature = 'laravel-model-reflection';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
