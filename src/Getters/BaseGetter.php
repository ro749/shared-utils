<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Database\Eloquent\Builder;
use Ro749\SharedUtils\Tables\Column;
use Ro749\SharedUtils\Filters\BaseFilter;
use Ro749\SharedUtils\Statistics\Statistic;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class BaseGetter
{
    public string $model_class = '';

     /** @var Column[] */
    public array $columns;

    /** @var BaseFilter[] */
    public array $filters;
    public array $backend_filters;

    /** @var Statistic[] */
    public array $statistics = [];

    function __construct(
        string $model_class = '',
        array $columns = [],
        array $statistics = [],
        array $filters = [], 
        array $backend_filters = []
    )
    {
        $this->model_class = $model_class;
        $this->columns = $columns;
        $this->statistics = $statistics;
        $this->filters = $filters;
        $this->backend_filters = $backend_filters;
    }

    function get_table(): string {
        $model_class = $this->model_class;
        $model = new $model_class();
        return $model->getTable();
    }

    public function get($start=null, $length=null, $search = '',$order = [],$filters = []): mixed
    {
        $search = $search==null?"":$search;
        $ans = [];
        $query = $this->get_query($ans,$search,$filters);
        foreach ($this->backend_filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsTotal'] = 1;//$query->count();
        $this->apply_filters($query, $filters);
        
        if ($search!="") {
            $query = $this->search($query,$search);
        }
        $ans['recordsFiltered'] = 1;//$query->count();
        if(!empty($order)){
            $query->orderBy(array_keys($this->columns)[$order['column']], $order['dir']);
        }
        if($length != -1){
            $query->offset($start);
            $query->limit($length);
        }
        DB::enableQueryLog();
        $ans['data'] = $query->get();
        Log::debug(DB::getQueryLog());
        return $ans;
    }

    function get_query(array &$ans,string $search,array $filters): Builder{
        $table = $this->get_table();
        $query = $this->model_class::query()->select($table.'.id');//DB::table($table)->select($table.'.id');
        $joins = [];
        foreach ($this->statistics as $key => $subquery) {
            $subquery->get_subquery($query,$table,$key,$filters);
        }
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
        return $query;
    }

    public function search(Builder $query,string $search): Builder{
        $table = $this->get_table();
        $query->where(function ($query) use ($search,$table) {
            foreach ($this->columns as $key => $column) {
                if ($column->is_foreign()) {
                    $modifier = $column->logic_modifier;
                    $query->orWhereRaw($modifier->get_value($table ,$key)." LIKE ?", ["%{$search}%"]);
                }
                else {
                    $query->orWhere($table . '.' . $key, 'like', '%' . $search . '%');
                }
            }
        });
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

    
    public function apply_filters($query, $filters){
        foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
    }
}