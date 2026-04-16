<?php

namespace Ro749\SharedUtils\Controllers;
use Illuminate\Support\Facades\Log;
abstract class Controller
{
    public static function instance(): Controller
    {
        $basename = class_basename(static::class);
        return new (config('overrides.controllers.'.$basename) ?? static::class);
    }

    public function get_default_args($function){
        return [];
    }
}
