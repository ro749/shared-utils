<?php

namespace Ro749\SharedUtils\Controllers;

abstract class Controller
{
    public static function instance(): Controller
    {
        $basename = class_basename(static::class);
        return config('controllers.'.$basename) ?? static::class;
    }
}
