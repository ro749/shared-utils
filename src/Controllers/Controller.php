<?php

namespace Ro749\SharedUtils\Controllers;
use Illuminate\Support\Facades\Log;
abstract class Controller
{
    public static function instance(): Controller
    {
        $basename = class_basename(static::class);
        Log::debug(config('overrides.controllers.'.$basename));
        return config('overrides.controllers.'.$basename) ?? static::class;
    }
}
