<?php
namespace Ro749\SharedUtils\Filters;
use \Illuminate\Database\Query\Builder; 
use Ro749\SharedUtils\Inputs\Selector;
use Illuminate\Http\Request;
class DateFilter extends BaseFilter
{
    public string $table;
    public string $column;

    public function __construct(string $display, string $id, string $table, string $column)
    {
        parent::__construct($display, $id);
        $this->table = $table;
        $this->column = $column;
    }

    public function filter(Builder $query,array $filters)
    {
        if(isset($filters["df-".$this->id])){
            $query->where($this->column, "=", $filters["df-".$this->id] ?? null);
        }
    }
    public function render(): string
    {
        return view('shared-utils::components.filters.date-filter')->with('filter', $this);
    }
}