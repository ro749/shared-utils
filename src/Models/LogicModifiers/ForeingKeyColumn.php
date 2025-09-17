<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;

class ForeingKeyColumn{
    public string $column;
    public function __construct(string $column){
        $this->column = $column;
    }
    public function get_value(string $table):string{
        return $table . '.' . $this->column;
    }
}