<?php

namespace Ro749\SharedUtils\Tables;

use Closure;

class Filters
{
    public string $display;
    public array $filters = [];
    public function __construct(string $display, array $filters)
    {
        $this->display = $display;
        $this->filters = $filters;
    }
}