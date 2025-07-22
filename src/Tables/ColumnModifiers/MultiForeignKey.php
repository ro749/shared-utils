<?php

namespace Ro749\SharedUtils\Tables\ColumnModifiers;

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
    public function type(): string
    {
        return 'multi_foreign_key';
    }

    public function get_value($key):string{
        $ans = 'CASE ';
        foreach ($this->columns as $column_key => $column_value) {
            $ans .= 'WHEN '.$this->table.'.'.$key.' = '.$column_key.' THEN '.$this->table.'.'.$column_value.' ';
        }
        $ans .= 'END ';
        return $ans;
    }
}