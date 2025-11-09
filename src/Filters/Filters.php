<?php

namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Eloquent\Builder; 
use Illuminate\View\View;
use Illuminate\Http\Request;
class Filters extends BaseFilter
{
    public array $filters = [];
    public function __construct(string $display,string $id, array $filters)
    {
        parent::__construct($display, $id,'');
        $this->filters = $filters;
    }

    public function filter(Builder $query,array $filters)
    {
        if(isset($filters[$this->id])){
            ($this->filters[$filters[$this->id]]->filter)($query);
        }
    }

    public function render(): View
    {
        return view('shared-utils::components.filters.filters')->with('filter', $this);
    }
}