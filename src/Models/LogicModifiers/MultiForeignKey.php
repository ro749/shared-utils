<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
class MultiForeignKey extends LogicModifier
{
    public string $key_column;
    public string $table;
    public array $columns;

    public string $type = 'multi_foreign_key';

    public function __construct(string $key_column, string $table, array $columns)
    {
        $this->key_column = $key_column;
        $this->table = $table;
        $this->columns = $columns;
    }

    public function  get_value(string $table, string $key):string{
        $ans = 'CASE ';
        foreach ($this->columns as $column_key => $column_value) {
            $ans .= 'WHEN '.$table.'.'.$key.' = '.$column_key.' THEN '.$column_value->get_value($this->table).' ';
        }
        $ans .= 'END ';
        return $ans;
    }
    
}