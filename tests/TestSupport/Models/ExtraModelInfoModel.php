<?php

namespace Spatie\ModelInfo\Tests\TestSupport\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraModelInfoModel extends Model
{
    public function extraModelInfo()
    {
        return 'extra info';
    }
}
