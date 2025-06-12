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

    public bool $is_editable = false;

    public function __construct(string $id, string $table, array $columns, View $view = null, Delete $delete = null)
    {
        $this->id = $id;
        $this->table = $table;
        $this->columns = $columns;
        $this->view = $view;
        $this->delete = $delete;
        $this->is_editable = $this->has_edit();
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

    public function get(): mixed
    {
        $query = DB::table($this->table)->select($this->table.'.id');
        $joins = [];
        foreach ($this->columns as $key => $column) {
            if ($column->is_foreign()) {
                if(!in_array($column, $joins)){
                    $joins[] = $column;
                    $query->join($column->table, $column->table . '.id', '=', $this->table . '.' . $key);
                }
                $query->addSelect($column->table . '.' . $column->column . ' as ' . $key);
            }
            else {
                $query->addSelect($this->table . '.' . $key);
            }
        }

        return $query->get();
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