<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Query\Builder;
use Ro749\SharedUtils\Tables\ColumnModifier;

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

    function get_query(array &$ans,string $search): Builder{
        $query = DB::table($this->table)->select($this->table.'.id');
        $joins = [];
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
                            $column->table . '.id', '=', $this->table . '.' . $key);
                    }
                    $query->addSelectRaw($modifier->get_value($key) . ' as ' . $key);
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
            else {
                $query->addSelect($this->table . '.' . $key);
            }
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

    public function search(Builder $query,string $search): Builder{
        $query->where(function ($query) use ($search) {
            foreach ($this->columns as $key => $column) {
                if ($column->is_foreign()) {
                    $modifier = $column->logic_modifier;
                    $query->orWhereRaw($modifier->get_value($key)." LIKE ?", ["%{$search}%"]);
                }
                else {
                    $query->orWhere($this->table . '.' . $key, 'like', '%' . $search . '%');
                }
            }
        });
        return $query;
    }
}