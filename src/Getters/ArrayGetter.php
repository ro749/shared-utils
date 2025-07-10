<?php

namespace Ro749\SharedUtils\Getters;

use Illuminate\Support\Facades\DB;

use Illuminate\Database\Query\Builder;

class ArrayGetter extends BaseGetter{

    public function __construct(string $table, array $columns, array $filters = [], array $backend_filters = [])
    {
        parent::__construct(
            filters:$filters, 
            backend_filters:$backend_filters,
            columns:$columns,
            table: $table
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
                    //if column needs data from other table and its not editable and this join has not been added, adds it
                    if(!in_array($column, $joins)){
                        $joins[] = $column;
                        $query->leftJoin($column->table, $column->table . '.id', '=', $this->table . '.' . $key);
                    }
                    //if column needs data from other table and its not editable adds the select of the column just as the key
                    $query->addSelect($column->table . '.' . $column->column . ' as ' . $key);
                }
                //if column needs data from other table and its editable
                else{
                    //if column needs data from other table and its editable gets all the column of the other table for the selector
                    //and saves it in $ans["selectors"]
                    $foreign_column = DB::table($column->table)->select('id',$column->column)->get();
                    foreach($foreign_column as $foreign_column_key => $foreign_column_value) {
                        $ans["selectors"][$key][$foreign_column_value->id] = $foreign_column_value->{$column->column};
                    }
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

    public function search(Builder $query,string $search){
        if ($search!='') {
            $query->where(function ($query) use ($search) {
                foreach ($this->columns as $key => $column) {
                    if ($column->is_foreign()) {
                        $query->orWhere($column->table . '.' . $column->column, 'like', '%' . $search . '%');
                    }
                    else {
                        $query->orWhere($this->table . '.' . $key, 'like', '%' . $search . '%');
                    }
                }
                
            });
        }
    }
}