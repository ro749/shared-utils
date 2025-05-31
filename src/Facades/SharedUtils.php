<?php

namespace Ro749\SharedUtils\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ro749\SharedUtils\SharedUtils
 */
class SharedUtils extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Ro749\SharedUtils\SharedUtils::class;
    }
}
