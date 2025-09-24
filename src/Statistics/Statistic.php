<?php

namespace Ro749\SharedUtils\Statistics;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Filters\BaseFilter;
//used for generate subqueries for statistics
class Statistic{
    public string $table = "";

    public string $group_column = "";

    /** @var StatisticColumn[] */
    public array $columns = [];

    /** @var BaseFilter[] */
    public array $filters;

    public function __construct(string $table, string $group_column, array $columns, array $filters = []){
        $this->table = $table;
        $this->group_column = $group_column;
        $this->columns = $columns;
        $this->filters = $filters;
    }

    public function get_subquery($query,$table,$name,$filters){
        $subquery = 
            DB::table($this->table)->
            select($this->group_column)->
            groupBy($this->group_column);
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
        $query->leftJoinSub($subquery, $name, function ($join) use ($table,$name) {
            $join->on($name.'.'.$this->group_column, '=', $table . '.id');
        });
    }
}