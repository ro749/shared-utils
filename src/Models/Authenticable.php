<?php

namespace Ro749\SharedUtils\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
class Authenticable extends Authenticatable
{
    public static function instance(): Model
    {
        $basename = class_basename(static::class);
        return new (config('overrides.models.'.$basename));
    }

    public static function get_class(): string
    {
        $basename = class_basename(static::class);
        return config('overrides.models.'.$basename);
    }
}
