<?php

namespace Ro749\SharedUtils\Models\LogicModifiers;

class ForeingKeyValue extends ForeingKeyColumn{
    public function __construct(string $value){
        parent::__construct($value);
    }
    public function get_value(string $table):string{
        return "'".$this->column."'";
    }
}