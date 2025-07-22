<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Illuminate\View\View;
abstract class BaseFilter
{
    public string $id;
    public string $display;
    public string $session;
    public function __construct(string $display, string $id,string $session = '')
    {
        $this->display = $display;
        $this->id = $id;
        $this->session = $session;
    }

    abstract function filter(Builder $query,array $filters);

    abstract function render(): View;
}