<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Closure;
class BasicFilter extends BaseFilter
{
    public Closure $filter;
    public function __construct(string $id,Closure $filter)
    {
        parent::__construct("", $id);
        $this->filter = $filter;
    }

    public function filter(Builder $query,array $filters)
    {
        ($this->filter)($query,$filters[$this->id]);
    }
}