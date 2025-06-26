<?php
namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Ro749\SharedUtils\Inputs\Selector;
use Illuminate\Http\Request;
class CategoryFilter extends BaseFilter
{
    public Selector $selector;

    public function __construct(string $display, string $id, Selector $selector)
    {
        parent::__construct($display, $id);
        $this->selector = $selector;
    }

    public function filter(Builder $query,array $filters)
    {
        if(isset($filters[$this->selector->id])){
            $query->where($this->selector->get_column(), "=", $filters[$this->selector->id] ?? null);
        }
    }

    public function render(): string
    {
        return view('shared-utils::components.filters.category-filter')->with('filter', $this);
    }
}