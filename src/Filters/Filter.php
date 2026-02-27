<?php

namespace Ro749\SharedUtils\Filters;

use Closure;

class Filter
{
    public string $display;
    public function __construct(string $display)
    {
        $this->display = $display;
    }
}