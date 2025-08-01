<?php

namespace Ro749\SharedUtils\Filters\BackendFilters;
use \Illuminate\Database\Query\Builder; 
use Illuminate\Support\Facades\Auth;
class UserFilter extends BackendFilter
{
    public string $column;
    public string $guard;
    public function __construct(string $id,string $column,string $guard = 'web')
    {
        parent::__construct($id);
        $this->column = $column;
        $this->guard = $guard;
    }

    public function filter(Builder $query)
    {
        $query->where($this->column, "=", Auth::guard($this->guard)->user()->id);
    }
}