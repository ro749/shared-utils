<?php

namespace Ro749\SharedUtils\Tables;

class Column
{
    //what is going to display in the frontend table
    public string $display;

    public ?ColumnModifier $modifier;

    public function __construct(string $display, ColumnModifier $modifier = null)
    {
        $this->display = $display;
        $this->modifier = $modifier;
    }
}