<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Illuminate\View\View;
abstract class BaseFilter
{
    public string $id;
    public string $display;
    public function __construct(string $display, string $id)
    {
        $this->display = $display;
        $this->id = $id;
    }

    abstract function filter(Builder $query,array $filters);

    abstract function render(): View;
}