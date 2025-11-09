<?php

namespace Ro749\SharedUtils\Models;
use Ro749\SharedUtils\Models\Scopes\UserScope;
use Illuminate\Support\Facades\Log;
trait UserOwned
{
    protected static function bootUserOwned()
    {
        Log::debug("bootUserOwned");
        static::addGlobalScope(new UserScope());
    }
}