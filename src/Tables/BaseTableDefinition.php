<?php
namespace Ro749\SharedUtils\Tables;

use Illuminate\Support\Facades\DB;

class BaseTableDefinition
{
    //the id the table is going to have
    public string $id;

    //the table name
    public string $table;

    /** var array<string, Column> */
    public array $columns = [];

    public ?View $view = null;
    public ?Delete $delete = null;

    public bool $needs_buttons = false;
    public bool $is_editable = false;
    public array $filters;


    public function __construct(
        string $id, 
        string $table, 
        array $columns, 
        View $view = null, 
        Delete $delete = null, 
        array $filters = []
    )
    {
        $this->id = $id;
        $this->table = $table;
        $this->columns = $columns;
        $this->view = $view;
        $this->delete = $delete;
        $this->filters = $filters;
        $this->is_editable = $this->has_edit();
        $this->needs_buttons = $this->needsButtons();
    }

    public function getColumn(string $key): ?Column
    {
        return $this->columns[$key] ?? null;
    }

    public function getColumnKeys(): array
    {
        return array_keys($this->columns);
    }

    public function needsButtons(): bool
    {
        return $this->view || $this->delete || $this->is_editable;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        $query = DB::table($this->table)->select($this->table.'.id');
        $joins = [];
        foreach ($this->columns as $key => $column) {
            if ($column->is_foreign()) {
                if(!$column->editable){
                    if(!in_array($column, $joins)){
                        $joins[] = $column;
                        $query->leftJoin($column->table, $column->table . '.id', '=', $this->table . '.' . $key);
                    }
                    $query->addSelect($column->table . '.' . $column->column . ' as ' . $key);
                }
                else{
                    $foreign_column = DB::table($column->table)->select('id',$column->column)->get();
                    foreach($foreign_column as $foreign_column_key => $foreign_column_value) {
                        $ans["selectors"][$key][$foreign_column_value->id] = $foreign_column_value->{$column->column};
                    }
                    $query->addSelect($this->table . '.' . $key);
                    if ($search) {
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
        $ans['recordsTotal'] = $query->count();
        
        if ($search) {
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
       foreach ($this->filters as $filter) {
            $filter->filter($query, $filters);
        }
        $ans['recordsFiltered'] = $query->count();
        
        $ans['data'] = $query->orderBy(array_keys($this->columns)[$order['column']], $order['dir'])->offset($start)->limit($length)->get();
        
        
        return $ans;
    }

    function save($id,$args) {
        DB::table($this->table)->where('id', $id)->update($args);
    }

    public function delete(int $id): void
    {
        DB::table($this->table)->where('id', $id)->delete();
    }

    public function has_edit(): bool
    {
        foreach ($this->columns as $key => $column) {
            if ($column->editable) {
                return true;
            }
        }
        return false;
    }
}