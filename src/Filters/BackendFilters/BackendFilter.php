<?php

namespace Ro749\SharedUtils\Filters\BackendFilters;
use \Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class BackendFilter
{
    public string $id;
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    abstract public function filter(Builder $query,array $filters);
}