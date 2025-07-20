<?php
namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Ro749\SharedUtils\FormRequests\Selector;
use Illuminate\Http\Request;
use Illuminate\View\View;
class CategoryFilter extends BaseFilter
{
    public string $column;
    public Selector $selector;

    public function __construct(string $display, string $id, Selector $selector, string $column)
    {
        parent::__construct($display, $id);
        $this->selector = $selector;
        $this->column = $column;
    }

    public function filter(Builder $query,array $filters)
    {
        if(isset($filters["cf-".$this->id])){
            $query->where($this->column, "=", $filters["cf-".$this->id] ?? null);
        }
    }

    public function render(): View
    {
        return view('shared-utils::components.filters.category-filter')->with('filter', $this);
    }
}