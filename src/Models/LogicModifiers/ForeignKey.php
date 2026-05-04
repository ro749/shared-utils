<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
class ForeignKey extends LogicModifier
{
    public string $table = '';
    public string $model_class = '';
    public string $column;
    public string $type = 'foreign_key';
    public function get_table(): string { 
        return empty($this->model_class) ? $this->table : ($this->model_class)::make()->getTable();
    }
    public function __construct(string $table = '',string $model_class='', string $column='')
    {
        $this->table = $table;
        $this->model_class = $model_class;
        $this->column = $column;
    }

    public function get_value(string $table, string $key):string{
        return $this->get_table() . '.' . $this->column;
    }
}