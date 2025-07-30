<?php

namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Models\Modifier;
use Ro749\SharedUtils\Models\LogicModifiers\LogicModifier;

class Column
{
    //what is going to display in the frontend table
    public string $display;

    public ?Modifier $modifier;

    public ?LogicModifier $logic_modifier;

    public bool $editable = false;

    public function __construct(string $display, Modifier $modifier = null, LogicModifier $logic_modifier = null)
    {
        $this->display = $display;
        $this->modifier = $modifier;
        $this->logic_modifier = $logic_modifier;
    }

    public function is_foreign(): bool
    {
        return 
        $this->logic_modifier !== null && (
        $this->logic_modifier->type == 'foreign_key' || 
        $this->logic_modifier->type == 'multi_foreign_key');
    }
}