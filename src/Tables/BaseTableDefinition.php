<?php
namespace Ro749\SharedUtils\Tables;

use Illuminate\Support\Facades\DB;
use Ro749\SharedUtils\Getters\BaseGetter;

class BaseTableDefinition
{
    //the id the table is going to have
    public string $id;

    public BaseGetter $getter;

    public ?View $view = null;
    public ?Delete $delete = null;

    public bool $needs_buttons = false;
    public bool $is_editable = false;


    public function __construct(
        string $id, 
        BaseGetter $getter,
        View $view = null, 
        Delete $delete = null
    )
    {
        $this->id = $id;
        $this->getter = $getter;
        $this->view = $view;
        $this->delete = $delete;
        $this->is_editable = $this->has_edit();
        $this->needs_buttons = $this->needsButtons();
    }

    public function getColumn(string $key): ?Column
    {
        return $this->getter->columns[$key] ?? null;
    }

    public function getColumnKeys(): array
    {
        return array_keys($this->getter->columns);
    }

    public function needsButtons(): bool
    {
        return $this->view || $this->delete || $this->is_editable;
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        return $this->getter->get($start, $length, $search,$order,$filters);
    }

    function save($id,$args) {
        DB::table($this->getter->table)->where('id', $id)->update($args);
    }

    public function delete(int $id): void
    {
        DB::table($this->getter->table)->where('id', $id)->delete();
    }

    public function has_edit(): bool
    {
        foreach ($this->getter->columns as $key => $column) {
            if ($column->editable) {
                return true;
            }
        }
        return false;
    }

    function get_columns(): array {
        return $this->getter->columns;
    }

    function get_info(){
        return [
            'id' => $this->id,
            'table' => $this->getter->table,
            'columns' => $this->getter->columns,
            'filters' => $this->getter->filters,
            'backend_filters' => $this->getter->backend_filters,
            'view' => $this->view,
            'delete' => $this->delete,
            'needs_buttons' => $this->needs_buttons,
            'is_editable' => $this->is_editable,
        ];
    }
}