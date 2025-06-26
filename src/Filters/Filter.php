<?php

namespace Ro749\SharedUtils\Filters;

use Closure;

class Filter
{
    public string $display;
    public Closure $filter;
    public function __construct(string $display, Closure $filter)
    {
        $this->display = $display;
        $this->filter = $filter;
    }
}