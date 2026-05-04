<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
class MultiForeignKey extends LogicModifier
{
    public string $key_column;
    public string $table;
    public string $model_class = '';
    public array $columns;

    public string $type = 'multi_foreign_key';

    public function __construct(string $key_column='', string $model_class='', string $table='', array $columns=[])
    {
        $this->key_column = $key_column;
        $this->model_class = $model_class;
        $this->table = $table;
        $this->columns = $columns;
    }

    public function  get_value(string $table, string $key):string{
        $ans = 'CASE ';
        foreach ($this->columns as $column_key => $column_value) {
            $ans .= 'WHEN '.$table.'.'.$key.' = '.$column_key.' THEN '.$column_value->get_value($this->get_table()).' ';
        }
        $ans .= 'END ';
        return $ans;
    }

    public function get_table(): string {
        return empty($this->model_class) ? $this->table : ($this->model_class)::make()->getTable();
    }
    
}