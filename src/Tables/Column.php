<?php

namespace Ro749\SharedUtils\Tables;

class Column
{
    //what is going to display in the frontend table
    public string $display;

    public ?ColumnModifier $modifier;

    //if the column is a foreign key, the table is the table of the foreign key
    public string $table;
    //if the column is a foreign key, the column is the column of the other table to use here
    public string $column;

    //the name of the options variable to use
    public string $options;

    //if this column can be edited
    public bool $editable;

    public function __construct(string $display, ColumnModifier $modifier = null, string $table = "", string $column = "", string $options = "", bool $editable = false)
    {
        $this->display = $display;
        $this->modifier = $modifier;
        $this->table = $table;
        $this->column = $column;
        $this->options = $options;
        $this->editable = $editable;
    }

    public function is_foreign(): bool
    {
        return $this->table != "" && $this->column != "";
    }
}