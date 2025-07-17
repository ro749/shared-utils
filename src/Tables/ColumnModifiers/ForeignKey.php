<?php

namespace Ro749\SharedUtils\Tables\ColumnModifiers;
class ForeignKey extends LogicModifier
{
    public string $table;
    public string $column;

    public function __construct(string $table, string $column)
    {
        $this->table = $table;
        $this->column = $column;
    }
    public function type(): string
    {
        return 'foreign_key';
    }

    public function get_value($key):string{
        return $this->table . '.' . $this->column;
    }
}