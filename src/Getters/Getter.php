<?php

namespace Ro749\SharedUtils\Getters;

use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Statistics\Statistic;
use Illuminate\Support\Facades\DB;
class Getter{

     /** @var Column[] */
    public array $columns;

    /** @var BaseFilter[] */
    public array $filters;
    public array $backend_filters;

    /** @var Statistic[] */
    public array $statistics = [];

    public bool $debug = false;

    function __construct(
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = [],
        bool $debug = false
    )
    {
        $this->columns = $columns;
        $this->statistics = $statistics;
        $this->filters = $filters;
        $this->backend_filters = $backend_filters;
        $this->debug = $debug;
    }

    function apply_statistics($query,$table,$filters = []){
        foreach ($this->statistics as $key => $subquery) {
            $subquery->get_subquery($query,$table,$key,$filters);
        }
    }

    function prosses_columns($query,$table,&$joins,$search){
        foreach ($this->columns as $key => $column) {
            if($column->local) continue;
            //if column needs data from other table
            if ($column->is_foreign()) {
                if(array_key_exists($column->logic_modifier->table, $this->statistics)){
                    $stat_name = $column->logic_modifier->table;
                    $query->addSelect(DB::raw('COALESCE('.$stat_name.'.'.$key.',0) as '.$key));
                    continue;
                }

                //if column needs data from other table and its not editable
                if(!$column->editable){
                    $modifier = $column->logic_modifier;
                    //if column needs data from other table and its not editable and this join has not been added, adds it
                    if(!in_array($modifier->table, $joins)){
                        $joins[] = $modifier->table;
                        $query->leftJoin(
                            $modifier->table, 
                            $modifier->table . '.id', '=', $table . '.' . $key);
                    }
                    $query->addSelect(DB::raw($modifier->get_value($table ,$key) . ' as ' . $key));
                }
                //if column needs data from other table and its editable
                else{
                    //if column needs data from other table and its editable it does not joins, as the join is going to be done manualy, the data 
                    //is already collected
                    $query->addSelect($table . '.' . $key);
                    //if column needs data from other table and its editable it does not joins, the join was not made, but if it has search it needs to
                    //be done so that it cans earch proprerly (the search is not aplied)
                    if ($search!='') {
                        if(!in_array($column, $joins)){
                            $joins[] = $column;
                            $query->leftJoin($column->table, $column->table . '.id', '=', $table . '.' . $key);
                        }
                    }
                }
            }
            else {
                $query->addSelect($table . '.' . $key);
            }
        }
    }

    

    
}