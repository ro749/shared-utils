<?php

namespace Ro749\SharedUtils\Statistics;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Filters\BackendFilters\BackendFilter;
//used for generate subqueries for statistics
class Statistic{
    public string $model_class = "";

    public string $group_column = "";

    /** @var StatisticColumn[] */
    public array $columns = [];

    /** @var BaseFilter[] */
    public array $filters;

    /** @var BackendFilter[] */
    public array $backend_filters;

    public ?StatisticLink $link;

    public function __construct(
            string $model_class, 
            string|StatisticLink $group_column, 
            array $columns, 
            array $filters = [], 
            array $backend_filters = [],
            StatisticLink $link = null
        ){
        $this->model_class = $model_class;
        $this->group_column = $group_column;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->link = $link;
        $this->backend_filters = $backend_filters;
    }

    public function get_table(): string
    {
        return ($this->model_class)::make()->getTable();
    }

    public function get_subquery($query,$table,$name,$filters){
        
        if(empty($this->link)){
            $subquery = 
                ($this->model_class)::query()->
                select($this->group_column)->
                groupBy($this->group_column);
        }
        else{
            $subquery = 
                ($this->link->model_class)::query()->
                select($this->link->column)->
                groupBy($this->link->column)->
                join(($this->model_class)::make()->getTable(), $this->link->get_table().'.id', '=', $this->get_table().'.'.$this->group_column);
        }
        
        foreach ($this->filters as $filter) {
            $filter->filter($subquery, $filters);
        }
        foreach ($this->columns as $key => $column) {
            $col = empty($column->column)?$key:$column->column;
            switch ($column->type) {
                case StatisticType::COUNT:
                    if(empty($column->filter)){
                        $str_stat = 'COUNT(*)';
                    }
                    else{
                        $str_stat = 'COUNT(CASE WHEN '.$column->filter.' THEN 1 END)';
                    }
                    break;
                case StatisticType::SUM:
                    if(empty($column->filter)){
                        $str_stat = 'SUM('.$col.')';
                    }
                    else{
                        $str_stat = 'SUM(CASE WHEN '.$column->filter.' THEN '.$col.' ELSE 0 END)';
                    }
                    break;
                case StatisticType::AVERAGE:
                    if(empty($column->filter)){
                        $str_stat = 'AVG('.$col.')';
                    }
                    else{
                        $str_stat = 'AVG(CASE WHEN '.$column->filter.' THEN '.$col.' ELSE 0 END)';
                    }
                    break;
            }
            $str_stat .= ' AS ' . $key;
            $subquery->addSelect(DB::Raw($str_stat));
            //$query->addSelect(DB::raw('COALESCE('.$key.'.'.$column["key"].',0) as '.$column["key"]));
        }

        foreach ($this->backend_filters as $filter) {
            $filter->filter($subquery, $filters);
        }

        if (method_exists($this->model_class, 'applyScopes')) {
            $this->model_class::applyScopes($subquery);
        }

        if(empty($this->link)){
            $query->leftJoinSub($subquery, $name, function ($join) use ($table,$name) {
                $join->on($name.'.'.$this->group_column, '=', $table . '.id');
            });
        }
        else{
            $query->leftJoinSub($subquery, $name, function ($join) use ($table,$name) {
                $join->on($name.'.'.$this->link->column, '=', $table . '.id');
            });
        }
        
    }
}