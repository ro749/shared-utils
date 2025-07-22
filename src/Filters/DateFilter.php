<?php
namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Ro749\SharedUtils\Inputs\Selector;
use Illuminate\Http\Request;
use Illuminate\View\View;
class DateFilter extends BaseFilter
{
    public string $table;
    public string $column;

    public function __construct(string $display, string $id, string $table, string $column, string $session = '')
    {
        parent::__construct($display, $id, $session);
        $this->table = $table;
        $this->column = $column;
    }

    public function filter(Builder $query,array $filters)
    {
        if(isset($filters["df-".$this->id])){
            $query->where($this->column, "=", $filters["df-".$this->id] ?? null);
        }
    }
    public function render(): View
    {
        return view('shared-utils::components.filters.date-filter')->with('filter', $this);
    }
}