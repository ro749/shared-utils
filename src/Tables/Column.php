<?php

namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Tables\ColumnModifiers\LogicModifier;
class Column
{
    //what is going to display in the frontend table
    public string $display;

    public ?ColumnModifier $modifier;

    public ?LogicModifier $logic_modifier;

    //if this column can be edited
    public bool $editable;

    public function __construct(string $display, ColumnModifier $modifier = null, LogicModifier $logic_modifier = null, bool $editable = false)
    {
        $this->display = $display;
        $this->modifier = $modifier;
        $this->logic_modifier = $logic_modifier;
        $this->editable = $editable;
    }

    public function is_foreign(): bool
    {
        return 
        $this->logic_modifier !== null && (
        $this->logic_modifier->type() == 'foreign_key' || 
        $this->logic_modifier->type() == 'multi_foreign_key');
    }
}