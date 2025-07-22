<?php

namespace Ro749\SharedUtils\Tables\ColumnModifiers;
class Enum extends LogicModifier
{
    public $options;

    public string $type = 'enum';
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function type(): string
    {
        return 'enum';
    }

    public function get_value($key):string{
        return $key;
    }
}