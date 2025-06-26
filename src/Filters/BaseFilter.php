<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
class BaseFilter
{
    public string $id;
    public string $display;
    public function __construct(string $display, string $id)
    {
        $this->display = $display;
        $this->id = $id;
    }

    public function filter(Builder $query,array $filters)
    {
        // This method should be overridden in subclasses
        // to apply the filter logic to the query.
        // For now, it does nothing.
        return $query;
    }
}