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

    }

    public function render(): string
    {
        return "";
    }
}