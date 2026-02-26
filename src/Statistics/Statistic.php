<?php

namespace Ro749\SharedUtils\Statistics;
use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Filters\BackendFilters\BackendFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
//used for generate subqueries for statistics
//this is for grouping the data table, the category table is the one that goes in the getter
class Statistic{
    public string $model_class = "";

    public string $group_column = "";

    /** @var StatisticColumn[] */
    public array $columns = [];

    /** @var BaseFilter[] */
    public array $filters;

    /** @var BackendFilter[] */
    public array $backend_filters;

    /** @var StatisticLink[] */
    public array $links = [];

    public function __construct(
            string $model_class, 
            string $group_column, 
            array $columns, 
            array $filters = [], 
            array $backend_filters = [],
            array $links = [],
        ){
        $this->model_class = $model_class;
        $this->group_column = $group_column;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->links = $links;
        $this->backend_filters = $backend_filters;
    }

    public function get_table(): string
    {
        return ($this->model_class)::make()->getTable();
    }

    public function get_query()
    {   
        $prev_table = ($this->model_class)::make()->getTable();
        $subquery = 
            ($this->model_class)::query()->
            select($prev_table.'.'.$this->group_column)->
            groupBy($prev_table.'.'.$this->group_column);
        $prev_table = ($this->model_class)::make()->getTable();
        $prev_tables = [$prev_table];
        foreach($this->links as $link){
            $link_table = $link->get_table();
            $as_table = $link_table;
            if(in_array($link_table,$prev_tables)){

                $as_table = $link_table.' as '.$link_table . '_' . count($prev_tables);
                $link_table .= '_' . count($prev_tables);
            }
            $subquery->join($as_table, $link_table.'.'.$link->column, '=', $prev_table.'.id');
            $prev_table = $link_table;
            $prev_tables[] = $link_table;
        }
        return $subquery;
    }
    
    /**
     * Summary of get_subquery
     * @param mixed $query: the parent query, the statistics will be added as subqueries
     * @param mixed $table the name of the table to use as a join, is going to be used as  join $table.id
     * @param mixed $name
     * @param mixed $filters
     * @return void
     */
    public function get_subquery($query,$table,$name,$filters){
        
        $subquery = $this->get_query();
        
        //foreach ($this->filters as $filter) {
        //    $filter->filter($subquery, $filters);
        //}
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
                case StatisticType::PERCENTAGE:
                    if(empty($column->filter)){
                        $str_stat = 'COUNT(*)*100.0/(SELECT COUNT(*) FROM '.$this->model_class::make()->getTable().')';
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

        if (method_exists($this->model_class, 'scope')) {
            $this->model_class::scope($subquery);
        }

        $subquery = $this->extra_process($subquery);

        $this->apply_join($query,$subquery,$table,$name);
    }

    public function extra_process($query){return $query;}

    public function apply_join($query,$subquery,$table,$name){
        $query->leftJoinSub($subquery, $name, function ($join) use ($table,$name) {
            $join->on($name.'.'.$this->group_column, '=', $table . '.id');
        });
    }
}