<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Query\Builder;
use Ro749\SharedUtils\Tables\ColumnModifier;
use function PHPUnit\Framework\isFinite;
use Ro749\SharedUtils\Statistics\StatisticType;
class ArrayGetter extends BaseGetter{

    public function __construct(string $table, array $columns, array $filters = [], array $backend_filters = [],$debug = false)
    {
        parent::__construct(
            filters:$filters, 
            backend_filters:$backend_filters,
            columns:$columns,
            table: $table,
            debug: $debug
        );
    }

    function get_query(array &$ans,string $search,array $filters): Builder{
        $query = DB::table($this->table)->select($this->table.'.id');
        $joins = [];
        $subqueries = [];
        foreach ($this->columns as $key => $column) {
            //if column needs data from other table
            if ($column->is_foreign()) {
                //if column needs data from other table and its not editable
                if(!$column->editable){
                    $modifier = $column->logic_modifier;
                    //if column needs data from other table and its not editable and this join has not been added, adds it
                    if(!in_array($modifier->table, $joins)){
                        $joins[] = $modifier->table;
                        $query->leftJoin(
                            $modifier->table, 
                            $modifier->table . '.id', '=', $this->table . '.' . $key);
                    }
                    $query->addSelect(DB::raw($modifier->get_value($this->table ,$key) . ' as ' . $key));
                }
                //if column needs data from other table and its editable
                else{
                    //if column needs data from other table and its editable it does not joins, as the join is going to be done manualy, the data 
                    //is already collected
                    $query->addSelect($this->table . '.' . $key);
                    //if column needs data from other table and its editable it does not joins, the join was not made, but if it has search it needs to
                    //be done so that it cans earch proprerly (the search is not aplied)
                    if ($search!='') {
                        if(!in_array($column, $joins)){
                            $joins[] = $column;
                            $query->leftJoin($column->table, $column->table . '.id', '=', $this->table . '.' . $key);
                        }
                    }
                }
            }
            else if ($column->is_subquery()) {
                $subquery_key = $column->logic_modifier->table."_".$column->logic_modifier->group_column;
                if(!array_key_exists($subquery_key, $subqueries)){
                    $subqueries[$subquery_key] = [];
                }
                $subqueries[$subquery_key][] = [
                    'stat' => $column->logic_modifier,
                    'key'=>$key
                ];
                
            }
            else {
                $query->addSelect($this->table . '.' . $key);
            }
        }
        foreach ($subqueries as $key => $columns) {
            $subquery = 
            DB::table($columns[0]["stat"]->table)->
            select($columns[0]["stat"]->group_column)->
            groupBy($columns[0]["stat"]->group_column);
            if(count($columns) == 1){
                $stat = $columns[0]["stat"];
                switch ($stat->statistic_type->value) {
                    case "count":
                        $str_stat = 'COUNT(*)';
                        break;
                    case "average":
                        $str_stat = 'AVG(' . $stat->data_column . ')';
                        break;
                    case "total":
                        $str_stat = 'SUM(' . $stat->data_column . ')';
                        break;
                }
                $str_stat .= ' AS ' . $columns[0]["key"];
                $subquery->select($str_stat)
                ->whereRaw($stat->filter);
                $query->addSelect(DB::raw('COALESCE('.$key.'.'.$columns[0]["key"].',0) as '.$columns[0]["key"]));
            }
            else{
                foreach ($columns as $column) {
                    $stat = $column["stat"];
                    switch ($stat->statistic_type->value) {
                        case "count":
                            $str_stat = 'COUNT(CASE WHEN '.$stat->filter.' THEN 1 END)';
                            break;
                        case "average":
                            $str_stat = 'AVG(CASE WHEN '.$stat->filter.' THEN '.$stat->data_column.' ELSE 0 END)';
                            break;
                        case "total":
                            $str_stat = 'SUM(CASE WHEN '.$stat->filter.' THEN '.$stat->data_column.' ELSE 0 END)';
                            break;
                    }
                    $str_stat .= ' AS ' . $column["key"];
                    $subquery->addSelect(DB::Raw($str_stat));
                    $query->addSelect(DB::raw('COALESCE('.$key.'.'.$column["key"].',0) as '.$column["key"]));
                }
            }
            $query->leftJoinSub($subquery, $key, function ($join) use ($key,$columns) {
                $join->on($key.'.'.$columns[0]["stat"]->group_column, '=', $this->table . '.id');
            });
            
        }
        return $query;
    }

    public function get_selectors(){
        $ans = [];
        foreach ($this->columns as $key => $column){
            if ($column->is_foreign() && $column->editable) {
                $modifier = $column->logic_modifier;
                $foreign_column = DB::table($modifier->table)->select('id',$modifier->column)->get();
                foreach($foreign_column as $foreign_column_key => $foreign_column_value) {
                    $ans[$key][$foreign_column_value->id] = $foreign_column_value->{$modifier->column};
                }
            }
        }
        return $ans;
    }

    public function needs_selectors(): bool{
        foreach ($this->columns as $key => $column){
            if ($column->is_foreign() && $column->editable) {
                return true;
            }
        }
        return false;
    }

    public function search(Builder $query,string $search): Builder{
        $query->where(function ($query) use ($search) {
            foreach ($this->columns as $key => $column) {
                if ($column->is_foreign()) {
                    $modifier = $column->logic_modifier;
                    $query->orWhereRaw($modifier->get_value($this->table ,$key)." LIKE ?", ["%{$search}%"]);
                }
                else {
                    $query->orWhere($this->table . '.' . $key, 'like', '%' . $search . '%');
                }
            }
        });
        return $query;
    }

    public function apply_filters($query, $filters){
        foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
    }
}