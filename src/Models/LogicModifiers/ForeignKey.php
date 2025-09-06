<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;
class ForeignKey extends LogicModifier
{
    public string $table;
    public string $column;
    public string $type = 'foreign_key';
    public function __construct(string $table, string $column)
    {
        $this->table = $table;
        $this->column = $column;
    }

    public function get_value(string $table, string $key):string{
        return $this->table . '.' . $this->column;
    }
}