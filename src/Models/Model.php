<?php

namespace Ro749\SharedUtils\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
class Model extends BaseModel
{
    public static function instance(): Model
    {
        $basename = class_basename(static::class);
        return new (config('overrides.models.'.$basename));
    }
}
